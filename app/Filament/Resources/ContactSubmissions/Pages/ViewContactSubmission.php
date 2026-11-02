<?php

namespace App\Filament\Resources\ContactSubmissions\Pages;

use App\Enums\ContactSubmissionClassification;
use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use App\Models\ContactSubmission;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewContactSubmission extends ViewRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->getRecord()->read_at === null) {
            $this->getRecord()->update(['read_at' => now()]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                $this->classifyAction(ContactSubmissionClassification::Valid, Heroicon::CheckCircle),
                $this->classifyAction(ContactSubmissionClassification::Spam, Heroicon::XCircle),
                $this->classifyAction(ContactSubmissionClassification::Unanswerable, Heroicon::ExclamationTriangle),
            ])
                ->label('Classify')
                ->icon(Heroicon::ChevronDown)
                ->button(),
            DeleteAction::make(),
        ];
    }

    private function classifyAction(ContactSubmissionClassification $classification, Heroicon $icon): Action
    {
        return Action::make('mark'.$classification->name)
            ->label($classification->getLabel())
            ->icon($icon)
            ->color($classification->getColor())
            ->action(fn (ContactSubmission $record): bool => $record->markClassification($classification, auth()->id()));
    }
}
