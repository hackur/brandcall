import { useFont } from '@/contexts/FontContext';
import { useState } from 'react';

interface FontPickerProps {
    position?: 'bottom-right' | 'bottom-left' | 'top-right' | 'top-left';
    showLabel?: boolean;
}

export default function FontPicker({ position = 'bottom-right', showLabel = true }: FontPickerProps) {
    const { currentFont, setFont, fontCombos, cycleFont } = useFont();
    const [isOpen, setIsOpen] = useState(false);

    const positionClasses = {
        'bottom-right': 'bottom-4 right-4',
        'bottom-left': 'bottom-4 left-4',
        'top-right': 'top-4 right-4',
        'top-left': 'top-4 left-4',
    };

    return (
        <div className={`fixed ${positionClasses[position]} z-50`}>
            {/* Toggle Button */}
            <button
                onClick={() => setIsOpen(!isOpen)}
                className="flex items-center gap-2 px-4 py-2 bg-slate-900/90 backdrop-blur-sm text-white rounded-lg shadow-lg hover:bg-slate-800 transition-colors border border-slate-700"
                title="Font Picker"
            >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h8m-8 6h16" />
                </svg>
                {showLabel && (
                    <span className="text-sm font-medium">{currentFont.name}</span>
                )}
            </button>

            {/* Dropdown Panel */}
            {isOpen && (
                <div className="absolute bottom-full right-0 mb-2 w-80 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden">
                    <div className="p-4 border-b border-slate-100 bg-slate-50">
                        <h3 className="font-semibold text-slate-900">Font Combinations</h3>
                        <p className="text-sm text-slate-500 mt-1">Select a font combo to preview</p>
                    </div>
                    
                    <div className="max-h-96 overflow-y-auto">
                        {fontCombos.map((combo) => (
                            <button
                                key={combo.id}
                                onClick={() => {
                                    setFont(combo.id);
                                    setIsOpen(false);
                                }}
                                className={`w-full text-left p-4 hover:bg-slate-50 transition-colors border-b border-slate-100 last:border-b-0 ${
                                    currentFont.id === combo.id ? 'bg-brand-50 border-l-4 border-l-brand-600' : ''
                                }`}
                            >
                                <div className="flex items-center justify-between">
                                    <span className="font-medium text-slate-900">{combo.name}</span>
                                    {currentFont.id === combo.id && (
                                        <span className="text-xs bg-brand-600 text-white px-2 py-0.5 rounded-full">
                                            Active
                                        </span>
                                    )}
                                </div>
                                <p className="text-sm text-slate-500 mt-1">{combo.description}</p>
                                
                                {/* Preview */}
                                <div className="mt-3 p-3 bg-slate-100 rounded-lg">
                                    <p 
                                        className="text-lg font-bold text-slate-900"
                                        style={{ fontFamily: `"${combo.headingFont}", sans-serif` }}
                                    >
                                        Heading Preview
                                    </p>
                                    <p 
                                        className="text-sm text-slate-600 mt-1"
                                        style={{ fontFamily: `"${combo.bodyFont}", sans-serif` }}
                                    >
                                        Body text looks like this in {combo.bodyFont}.
                                    </p>
                                </div>
                            </button>
                        ))}
                    </div>
                    
                    {/* Quick Actions */}
                    <div className="p-3 border-t border-slate-100 bg-slate-50 flex gap-2">
                        <button
                            onClick={cycleFont}
                            className="flex-1 px-3 py-2 text-sm font-medium text-brand-600 hover:bg-brand-50 rounded-lg transition-colors"
                        >
                            Cycle Fonts (→)
                        </button>
                        <button
                            onClick={() => setIsOpen(false)}
                            className="px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                        >
                            Close
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
