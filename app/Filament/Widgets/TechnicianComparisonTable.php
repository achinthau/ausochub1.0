<?php

namespace App\Filament\Widgets;

use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\CxTicket;
use Illuminate\Support\Facades\DB;

class TechnicianComparisonTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Technician Performance Comparison';
    
    protected static ?string $pollingInterval = null;

    protected function getTableQuery(): Builder
    {
        $startDate = request()->query('startDate', now()->subDays(30)->toDateString());
        $endDate = request()->query('endDate', now()->toDateString());

        return User::query()
            ->whereHas('technicianTickets', function ($query) use ($startDate, $endDate) {
                 // Filter by date range
                 $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                 
                 // Filter tickets by current tenant/company context if needed
                 $query->when(auth()->user()->tenant_context, function ($q) {
                     $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
                     $q->whereIn('company', $companyNames);
                 });
            })
            // Optimization: Load counts/aggregates
             ->withCount(['technicianTickets as total_tickets' => function ($query) use ($startDate, $endDate) {
                 $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                 $query->when(auth()->user()->tenant_context, function ($q) {
                     $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
                     $q->whereIn('company', $companyNames);
                 });
             }])
             ->withCount(['technicianTickets as rated_tickets' => function ($query) use ($startDate, $endDate) {
                 $query->whereNotNull('satisfaction_rate')
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->when(auth()->user()->tenant_context, function ($q) {
                     $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
                     $q->whereIn('company', $companyNames);
                 });
             }])
             ->withCount(['technicianTickets as rated_satisfied_tickets' => function ($query) use ($startDate, $endDate) {
                 $query->whereNotNull('satisfaction_rate')  
                    ->where('satisfaction_rate', '>=', 4)
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->when(auth()->user()->tenant_context, function ($q) {
                     $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
                     $q->whereIn('company', $companyNames);
                 });
             }])
             ->withCount(['technicianTickets as rated_unsatisfied_tickets' => function ($query) use ($startDate, $endDate) {
                 $query->whereNotNull('satisfaction_rate')  
                    ->where('satisfaction_rate', '<', 3)
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->when(auth()->user()->tenant_context, function ($q) {
                     $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
                     $q->whereIn('company', $companyNames);
                 });
             }])
             ->withCount(['technicianTickets as rated_neutral_tickets' => function ($query) use ($startDate, $endDate) {
                 $query->whereNotNull('satisfaction_rate')  
                    ->where('satisfaction_rate', '=', 3)
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->when(auth()->user()->tenant_context, function ($q) {
                     $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
                     $q->whereIn('company', $companyNames);
                 });
             }])
            // To get average rating, we might need a subquery or attribute
             ->withAvg(['technicianTickets as average_rating' => function ($query) use ($startDate, $endDate) {
                 $query->whereNotNull('satisfaction_rate')
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->when(auth()->user()->tenant_context, function ($q) {
                     $companyNames = array_filter(array_map('trim', explode(',', auth()->user()->tenant_context)));
                     $q->whereIn('company', $companyNames);
                 });
             }], 'satisfaction_rate');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Technician Name')
                ->searchable()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('total_tickets')
                ->label('Total Tickets')
                ->sortable(),

            Tables\Columns\TextColumn::make('rated_tickets')
                ->label('Total Rated Tickets')
                ->sortable(),

            Tables\Columns\TextColumn::make('rated_satisfied_tickets')
                ->label('Satisfied Tickets')
                ->sortable(),

            Tables\Columns\TextColumn::make('rated_unsatisfied_tickets')
                ->label('Unsatisfied Tickets')
                ->sortable(),

            Tables\Columns\TextColumn::make('rated_neutral_tickets')
                ->label('Neutral Tickets')
                ->sortable(),
                
            Tables\Columns\TextColumn::make('average_rating')
                ->label('Avg Satisfaction')
                ->sortable()
                ->formatStateUsing(fn ($state) => number_format($state)/5*100 . '%'),
        ];
    }
    
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view_details')
                ->label('View Details')
                ->icon('heroicon-s-eye')
                ->action(function (User $record) {
                    return redirect()->route('filament.pages.technician-performance-dashboard', ['technician' => $record->name, 'technician_id' => $record->id]);
                }),
        ];
    }
}
