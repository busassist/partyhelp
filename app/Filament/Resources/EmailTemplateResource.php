<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | \UnitEnum | null $navigationGroup = 'Manage System Data';

    protected static ?string $navigationLabel = 'Manage Emails';

    protected static ?string $modelLabel = 'Email Template';

    protected static ?string $pluralModelLabel = 'Email Templates';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            SchemaSection::make('Template')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]),
            SchemaSection::make('Delivery')
                ->description('Send this template via email; optionally also via WhatsApp when Twilio is configured.')
                ->schema([
                    Forms\Components\Checkbox::make('send_via_whatsapp')
                        ->label('Send via WhatsApp (in addition to email)')
                        ->helperText('When enabled and Twilio is configured, a WhatsApp message will be sent alongside the email where a recipient phone number is available.'),
                ])
                ->collapsible(),
            SchemaSection::make('WhatsApp message (lead opportunity)')
                ->description('Wording and button labels for the WhatsApp message with Accept / Ignore buttons. Only used when this template is sent via WhatsApp.')
                ->schema(static::whatsappLeadOpportunityFields())
                ->visible(fn (?EmailTemplate $record) => $record && in_array($record->key, ['lead_opportunity', 'lead_opportunity_discount'], true))
                ->collapsible(),
            SchemaSection::make('Editable content')
                ->description('These snippets are used in the email body. Use the editor for basic formatting (bold, italic, links).')
                ->schema(static::buildSlotEditors())
                ->collapsible(),
        ]);
    }

    /**
     * Form fields for WhatsApp lead-opportunity message (body + button labels).
     */
    protected static function whatsappLeadOpportunityFields(): array
    {
        return [
            Forms\Components\Textarea::make('whatsapp_body')
                ->label('Message body')
                ->helperText('Main text shown above the Accept / Ignore buttons. Plain text only.')
                ->rows(4)
                ->maxLength(1024)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('whatsapp_accept_label')
                ->label('Accept button label')
                ->maxLength(25)
                ->placeholder('Accept')
                ->helperText('Label for the button that accepts the lead (max 25 characters for WhatsApp).'),
            Forms\Components\TextInput::make('whatsapp_ignore_label')
                ->label('Ignore button label')
                ->maxLength(25)
                ->placeholder('Ignore')
                ->helperText('Label for the button that declines the lead (max 25 characters for WhatsApp).'),
        ];
    }

    /**
     * Build ProseMirror/Tiptap RichEditors for each content slot.
     * Toolbar restricted to bold, italic, link for tight control of email output.
     */
    protected static function buildSlotEditors(): array
    {
        $slots = config('partyhelp.email_template_slots', []);
        $editors = [];

        foreach ($slots as $templateKey => $slotLabels) {
            foreach ($slotLabels as $slotKey => $label) {
                $editors[] = Forms\Components\RichEditor::make("content_slots.{$slotKey}")
                    ->label($label)
                    ->toolbarButtons([['bold', 'italic', 'link'], ['bulletList', 'orderedList']])
                    ->columnSpanFull()
                    ->visible(fn (?EmailTemplate $record) => $record && $record->key === $templateKey);
            }
        }

        return $editors ?: [Forms\Components\Placeholder::make('no_slots')->content('No editable slots configured.')];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('key')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('subject')->limit(50)->searchable(),
                Tables\Columns\IconColumn::make('send_via_whatsapp')
                    ->label('WhatsApp')
                    ->boolean()
                    ->trueIcon('heroicon-o-chat-bubble-left-right')
                    ->falseIcon('heroicon-o-minus'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
