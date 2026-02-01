<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageResource\Pages;
use App\Models\LandingPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Landing Page')
                    ->tabs([
                        // Basic Info Tab
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Page Settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->prefix(url('/lp/')),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),
                                    ])->columns(2),
                            ]),

                        // Content Tab
                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\Section::make('Hero Section')
                                    ->schema([
                                        Forms\Components\TextInput::make('headline')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Increase Your Answer Rates by 300%'),
                                        Forms\Components\TextInput::make('subheadline')
                                            ->maxLength(255)
                                            ->placeholder('Trusted by 1000+ businesses'),
                                        Forms\Components\Textarea::make('description')
                                            ->rows(3)
                                            ->placeholder('Detailed description of your offering...'),
                                        Forms\Components\TextInput::make('cta_text')
                                            ->label('CTA Button Text')
                                            ->default('Get Started')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('cta_url')
                                            ->label('CTA URL')
                                            ->url()
                                            ->placeholder('https://...'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Media')
                                    ->schema([
                                        Forms\Components\FileUpload::make('hero_image')
                                            ->image()
                                            ->directory('landing-pages'),
                                        Forms\Components\TextInput::make('hero_video_url')
                                            ->url()
                                            ->placeholder('YouTube or Vimeo URL'),
                                    ])->columns(2),

                                Forms\Components\Section::make('Features')
                                    ->schema([
                                        Forms\Components\Repeater::make('features')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->required(),
                                                Forms\Components\Textarea::make('description')
                                                    ->rows(2),
                                                Forms\Components\TextInput::make('icon')
                                                    ->placeholder('heroicon name (e.g., check-circle)'),
                                            ])
                                            ->columns(3)
                                            ->defaultItems(3)
                                            ->collapsible(),
                                    ]),

                                Forms\Components\Section::make('Testimonials')
                                    ->schema([
                                        Forms\Components\Repeater::make('testimonials')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required(),
                                                Forms\Components\TextInput::make('title')
                                                    ->placeholder('CEO at Company'),
                                                Forms\Components\Textarea::make('quote')
                                                    ->required()
                                                    ->rows(2),
                                                Forms\Components\FileUpload::make('avatar')
                                                    ->image()
                                                    ->avatar()
                                                    ->directory('testimonials'),
                                            ])
                                            ->columns(2)
                                            ->collapsible(),
                                    ]),
                            ]),

                        // Design Tab
                        Forms\Components\Tabs\Tab::make('Design')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\Section::make('Layout & Colors')
                                    ->schema([
                                        Forms\Components\Select::make('layout_preset')
                                            ->options(LandingPage::LAYOUT_PRESETS)
                                            ->default('hero-left')
                                            ->required(),
                                        Forms\Components\Select::make('color_scheme')
                                            ->options(array_combine(
                                                array_keys(LandingPage::COLOR_SCHEMES),
                                                array_map('ucfirst', array_keys(LandingPage::COLOR_SCHEMES))
                                            ))
                                            ->default('blue')
                                            ->required(),
                                    ])->columns(2),

                                Forms\Components\Section::make('Custom Colors (Optional)')
                                    ->description('Override the color scheme with custom colors')
                                    ->schema([
                                        Forms\Components\ColorPicker::make('custom_colors.primary')
                                            ->label('Primary Color'),
                                        Forms\Components\ColorPicker::make('custom_colors.secondary')
                                            ->label('Secondary Color'),
                                        Forms\Components\ColorPicker::make('custom_colors.accent')
                                            ->label('Accent Color'),
                                        Forms\Components\ColorPicker::make('custom_colors.background')
                                            ->label('Background Color'),
                                        Forms\Components\ColorPicker::make('custom_colors.text')
                                            ->label('Text Color'),
                                    ])->columns(5),

                                Forms\Components\Section::make('Branding')
                                    ->schema([
                                        Forms\Components\FileUpload::make('logo_url')
                                            ->label('Logo')
                                            ->image()
                                            ->directory('landing-pages/logos'),
                                        Forms\Components\FileUpload::make('favicon_url')
                                            ->label('Favicon')
                                            ->image()
                                            ->directory('landing-pages/favicons'),
                                    ])->columns(2),
                            ]),

                        // Form Tab
                        Forms\Components\Tabs\Tab::make('Contact Form')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Forms\Components\Section::make('Form Settings')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_contact_form')
                                            ->label('Show Contact Form')
                                            ->default(true),
                                        Forms\Components\TextInput::make('form_headline')
                                            ->default('Get in Touch')
                                            ->maxLength(255),
                                    ])->columns(2),

                                Forms\Components\Section::make('Form Fields')
                                    ->schema([
                                        Forms\Components\Repeater::make('form_fields')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->placeholder('field_name'),
                                                Forms\Components\TextInput::make('label')
                                                    ->required()
                                                    ->placeholder('Field Label'),
                                                Forms\Components\Select::make('type')
                                                    ->options([
                                                        'text' => 'Text',
                                                        'email' => 'Email',
                                                        'tel' => 'Phone',
                                                        'textarea' => 'Textarea',
                                                        'select' => 'Select',
                                                        'checkbox' => 'Checkbox',
                                                    ])
                                                    ->default('text')
                                                    ->required(),
                                                Forms\Components\Toggle::make('required')
                                                    ->default(false),
                                            ])
                                            ->columns(4)
                                            ->defaultItems(0)
                                            ->collapsible(),
                                    ]),
                            ]),

                        // Pricing Tab
                        Forms\Components\Tabs\Tab::make('Pricing')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make('Pricing Section')
                                    ->schema([
                                        Forms\Components\Toggle::make('show_pricing')
                                            ->label('Show Pricing Section')
                                            ->default(false),
                                        Forms\Components\TextInput::make('pricing_headline')
                                            ->maxLength(255)
                                            ->placeholder('Simple, Transparent Pricing'),
                                    ])->columns(2),
                            ]),

                        // SEO Tab
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\Section::make('Meta Tags')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->maxLength(70)
                                            ->placeholder('Page title for search engines'),
                                        Forms\Components\Textarea::make('meta_description')
                                            ->maxLength(160)
                                            ->rows(2)
                                            ->placeholder('Description for search engines'),
                                        Forms\Components\FileUpload::make('og_image')
                                            ->label('Open Graph Image')
                                            ->image()
                                            ->directory('landing-pages/og'),
                                    ]),

                                Forms\Components\Section::make('UTM Tracking')
                                    ->schema([
                                        Forms\Components\TextInput::make('utm_source')
                                            ->placeholder('google'),
                                        Forms\Components\TextInput::make('utm_medium')
                                            ->placeholder('cpc'),
                                        Forms\Components\TextInput::make('utm_campaign')
                                            ->placeholder('spring_sale'),
                                    ])->columns(3),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->copyable()
                    ->copyMessage('URL copied!')
                    ->formatStateUsing(fn ($state) => "/lp/{$state}"),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('layout_preset')
                    ->badge()
                    ->label('Layout'),
                Tables\Columns\TextColumn::make('color_scheme')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'blue' => 'info',
                        'green' => 'success',
                        'purple' => 'gray',
                        'orange' => 'warning',
                        'red' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable()
                    ->label('Views'),
                Tables\Columns\TextColumn::make('conversion_count')
                    ->numeric()
                    ->sortable()
                    ->label('Leads'),
                Tables\Columns\TextColumn::make('conversion_rate')
                    ->suffix('%')
                    ->sortable()
                    ->label('Conv. Rate'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\SelectFilter::make('color_scheme')
                    ->options(array_combine(
                        array_keys(LandingPage::COLOR_SCHEMES),
                        array_map('ucfirst', array_keys(LandingPage::COLOR_SCHEMES))
                    )),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn (LandingPage $record) => "/lp/{$record->slug}")
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (LandingPage $record) {
                        $new = $record->replicate();
                        $new->name = $record->name . ' (Copy)';
                        $new->slug = $record->slug . '-copy-' . time();
                        $new->view_count = 0;
                        $new->conversion_count = 0;
                        $new->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingPages::route('/'),
            'create' => Pages\CreateLandingPage::route('/create'),
            'edit' => Pages\EditLandingPage::route('/{record}/edit'),
        ];
    }
}
