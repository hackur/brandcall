# DeepAgents + N8N + ComfyUI Integration Design

> Based on LangChain's deepagents (v0.3.11) for hierarchical agent workflows
> Date: 2026-02-04

## Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         N8N Workflow                             │
│  (Webhook trigger, scheduling, external integrations)           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DeepAgents (Python)                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │ Main Agent   │  │ Code Review  │  │ Image Gen    │          │
│  │ (Opus 4.5)   │──│ (Haiku 4.5)  │  │ (ComfyUI)    │          │
│  │              │  │              │  │              │          │
│  │ Planning     │  │ Fast feedback│  │ SDXL/Flux    │          │
│  │ Filesystem   │  │ Style checks │  │ Workflows    │          │
│  │ Shell access │  │ Lint rules   │  │              │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     ComfyUI Server                               │
│  - REST API (localhost:8188)                                     │
│  - Workflow execution                                            │
│  - Image generation (MPS/CUDA)                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Installation

```bash
# DeepAgents
pip install deepagents
# or
uv add deepagents

# CLI (optional - for interactive use)
uv tool install deepagents-cli
```

## DeepAgents Architecture

### Core Components

| Component | Purpose |
|-----------|---------|
| `create_deep_agent()` | Factory for creating agents |
| Planning tools | `write_todos` / `read_todos` for task breakdown |
| Filesystem tools | `read_file`, `write_file`, `edit_file`, `ls`, `glob`, `grep` |
| Shell access | `execute` for running commands (sandboxed) |
| Sub-agents | `task` for delegating work with isolated context |

### Agent Configuration

```python
from deepagents import create_deep_agent
from langchain.chat_models import init_chat_model

# Main orchestrator agent
main_agent = create_deep_agent(
    model=init_chat_model("anthropic:claude-opus-4-5"),
    skills=[".agents/skills/main/"],
    name="main",
    system_prompt="""You are the main orchestrator for BrandCall automation.
    You can delegate to specialized sub-agents for specific tasks."""
)

# Code review sub-agent (fast, cheap)
code_reviewer = {
    "name": "code_reviewer",
    "model": "anthropic:claude-haiku-4-5",
    "description": "Reviews code against style guide and best practices",
    "skills": [".agents/skills/code_reviewer/"],
}

# Image generation sub-agent
image_generator = {
    "name": "image_generator", 
    "model": "anthropic:claude-haiku-4-5",
    "description": "Generates images using ComfyUI workflows",
    "skills": [".agents/skills/comfyui/"],
    "tools": [comfyui_generate_tool],  # Custom tool
}

# Create deep agent with sub-agents
agent = create_deep_agent(
    model="anthropic:claude-opus-4-5",
    subagents=[code_reviewer, image_generator],
    skills=[".agents/skills/main/"],
    name="brandcall_orchestrator"
)
```

## ComfyUI Integration

### Custom Tool for DeepAgents

```python
# .agents/skills/comfyui/tools.py

import httpx
import json
import time
from typing import Optional
from langchain.tools import tool

COMFYUI_URL = "http://localhost:8188"

@tool
def generate_image(
    prompt: str,
    negative_prompt: str = "text, watermark, blurry, low quality",
    width: int = 512,
    height: int = 512,
    steps: int = 20,
    cfg: float = 7.5,
    seed: Optional[int] = None
) -> dict:
    """
    Generate an image using ComfyUI with SDXL.
    
    Args:
        prompt: Positive prompt describing the desired image
        negative_prompt: Things to avoid in the image
        width: Image width in pixels
        height: Image height in pixels
        steps: Number of sampling steps (more = better quality, slower)
        cfg: Classifier-free guidance scale (7-10 typical)
        seed: Random seed for reproducibility (None = random)
    
    Returns:
        dict with 'filename', 'path', and 'prompt_id'
    """
    import random
    
    if seed is None:
        seed = random.randint(0, 2**32 - 1)
    
    workflow = {
        "3": {
            "inputs": {
                "seed": seed,
                "steps": steps,
                "cfg": cfg,
                "sampler_name": "euler",
                "scheduler": "normal",
                "denoise": 1,
                "model": ["4", 0],
                "positive": ["6", 0],
                "negative": ["7", 0],
                "latent_image": ["5", 0]
            },
            "class_type": "KSampler"
        },
        "4": {
            "inputs": {"ckpt_name": "sd_xl_base_1.0.safetensors"},
            "class_type": "CheckpointLoaderSimple"
        },
        "5": {
            "inputs": {"width": width, "height": height, "batch_size": 1},
            "class_type": "EmptyLatentImage"
        },
        "6": {
            "inputs": {"text": prompt, "clip": ["4", 1]},
            "class_type": "CLIPTextEncode"
        },
        "7": {
            "inputs": {"text": negative_prompt, "clip": ["4", 1]},
            "class_type": "CLIPTextEncode"
        },
        "8": {
            "inputs": {"samples": ["3", 0], "vae": ["4", 2]},
            "class_type": "VAEDecode"
        },
        "9": {
            "inputs": {"filename_prefix": "deepagent_gen", "images": ["8", 0]},
            "class_type": "SaveImage"
        }
    }
    
    # Queue the workflow
    response = httpx.post(
        f"{COMFYUI_URL}/prompt",
        json={"prompt": workflow},
        timeout=30
    )
    result = response.json()
    prompt_id = result["prompt_id"]
    
    # Poll for completion
    for _ in range(120):  # 2 min timeout
        history = httpx.get(f"{COMFYUI_URL}/history/{prompt_id}").json()
        if prompt_id in history:
            outputs = history[prompt_id].get("outputs", {})
            if "9" in outputs:
                image_info = outputs["9"]["images"][0]
                return {
                    "filename": image_info["filename"],
                    "path": f"~/ComfyUI/output/{image_info['filename']}",
                    "prompt_id": prompt_id,
                    "seed": seed
                }
        time.sleep(1)
    
    return {"error": "Generation timed out", "prompt_id": prompt_id}


@tool
def list_comfyui_models() -> list:
    """List available checkpoint models in ComfyUI."""
    response = httpx.get(f"{COMFYUI_URL}/object_info/CheckpointLoaderSimple")
    data = response.json()
    return data["CheckpointLoaderSimple"]["input"]["required"]["ckpt_name"][0]


@tool
def get_comfyui_status() -> dict:
    """Get ComfyUI system status and queue info."""
    system = httpx.get(f"{COMFYUI_URL}/system_stats").json()
    queue = httpx.get(f"{COMFYUI_URL}/queue").json()
    return {
        "version": system["system"]["comfyui_version"],
        "device": system["devices"][0]["name"] if system["devices"] else "unknown",
        "queue_pending": len(queue.get("queue_pending", [])),
        "queue_running": len(queue.get("queue_running", []))
    }
```

## N8N Integration

### Webhook Trigger for DeepAgents

```javascript
// N8N HTTP Request node configuration
{
  "method": "POST",
  "url": "http://localhost:8000/agent/run",  // FastAPI wrapper
  "headers": {
    "Content-Type": "application/json"
  },
  "body": {
    "task": "{{ $json.task }}",
    "context": {
      "source": "n8n",
      "workflow_id": "{{ $workflow.id }}",
      "execution_id": "{{ $execution.id }}"
    }
  }
}
```

### FastAPI Wrapper for DeepAgents

```python
# agent_server.py
from fastapi import FastAPI, BackgroundTasks
from pydantic import BaseModel
from deepagents import create_deep_agent
import uuid

app = FastAPI()

# Initialize agent once
agent = create_deep_agent(
    model="anthropic:claude-opus-4-5",
    tools=[generate_image, list_comfyui_models, get_comfyui_status]
)

class AgentRequest(BaseModel):
    task: str
    context: dict = {}

class AgentResponse(BaseModel):
    run_id: str
    status: str
    result: dict = None

# Store results
results = {}

@app.post("/agent/run")
async def run_agent(request: AgentRequest, background_tasks: BackgroundTasks):
    run_id = str(uuid.uuid4())
    results[run_id] = {"status": "running"}
    
    background_tasks.add_task(execute_agent, run_id, request)
    
    return {"run_id": run_id, "status": "accepted"}

@app.get("/agent/status/{run_id}")
async def get_status(run_id: str):
    return results.get(run_id, {"status": "not_found"})

async def execute_agent(run_id: str, request: AgentRequest):
    try:
        result = agent.invoke({
            "messages": [{"role": "user", "content": request.task}]
        })
        results[run_id] = {
            "status": "complete",
            "result": result
        }
    except Exception as e:
        results[run_id] = {
            "status": "error",
            "error": str(e)
        }
```

## Skills Directory Structure

```
.agents/
├── skills/
│   ├── main/
│   │   ├── SKILL.md           # Main orchestrator instructions
│   │   └── prompts/
│   │       └── system.md      # System prompt
│   │
│   ├── code_reviewer/
│   │   ├── SKILL.md           # Code review guidelines
│   │   ├── style_guide.md     # PHP/Laravel style rules
│   │   └── lint_rules.json    # Custom lint patterns
│   │
│   ├── comfyui/
│   │   ├── SKILL.md           # ComfyUI usage instructions
│   │   ├── tools.py           # Custom tools (above)
│   │   └── workflows/
│   │       ├── sdxl_basic.json
│   │       ├── flux_dev.json
│   │       └── brandcall_product.json
│   │
│   └── numhub/
│       ├── SKILL.md           # NumHub API instructions
│       └── api_reference.md   # API endpoints
```

## Example Workflows

### 1. Generate Marketing Image

```python
result = agent.invoke({
    "messages": [{
        "role": "user",
        "content": """Generate a marketing image for BrandCall showing:
        - A modern smartphone with caller ID display
        - Business name "Acme Corp" shown on screen
        - Professional office background
        - High quality, 1024x1024"""
    }]
})
```

### 2. Code Review Pipeline

```python
result = agent.invoke({
    "messages": [{
        "role": "user", 
        "content": """Review the NumHub integration code in 
        /Volumes/JS-DEV/brandcall/app/Services/NumHub/
        
        Check for:
        - SOLID principles compliance
        - Error handling
        - Rate limiting implementation
        - Security issues"""
    }]
})
```

### 3. N8N Triggered Task

```
N8N Webhook → FastAPI → DeepAgent → [Code Review + Image Gen] → Webhook Callback
```

## Running the Stack

```bash
# Terminal 1: ComfyUI
cd ~/ComfyUI && source venv/bin/activate
python main.py --listen 0.0.0.0 --port 8188

# Terminal 2: DeepAgents API
cd /path/to/project
uvicorn agent_server:app --host 0.0.0.0 --port 8000

# Terminal 3: N8N (if self-hosted)
n8n start

# Or use DeepAgents CLI directly
ANTHROPIC_API_KEY=xxx deepagents
```

## Environment Variables

```bash
# .env
ANTHROPIC_API_KEY=sk-ant-xxx
OPENAI_API_KEY=sk-xxx  # Optional, for GPT models
COMFYUI_URL=http://localhost:8188
DEEPAGENTS_SKILLS_PATH=.agents/skills
```

## Next Steps

1. [ ] Create skills directory structure for BrandCall
2. [ ] Implement ComfyUI tools as custom LangChain tools
3. [ ] Set up FastAPI wrapper for N8N integration
4. [ ] Create N8N workflow templates
5. [ ] Test end-to-end with sample tasks
6. [ ] Add monitoring/logging (LangSmith)
7. [ ] Create BrandCall-specific workflows (product images, reports)

## Resources

- [DeepAgents Docs](https://docs.langchain.com/oss/python/deepagents/overview)
- [LangChain MCP Adapters](https://github.com/langchain-ai/langchain-mcp-adapters)
- [ComfyUI API](https://github.com/comfyanonymous/ComfyUI)
- [N8N Docs](https://docs.n8n.io/)
