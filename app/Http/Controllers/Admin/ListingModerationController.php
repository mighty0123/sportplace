<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Admin\ListingAlreadyProcessedException;
use App\Exceptions\Admin\ListingBlockedByChecksException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveListingRequest;
use App\Http\Requests\Admin\RejectListingRequest;
use App\Services\Admin\ListingChecks;
use App\Services\Admin\ListingModerationService as Moderation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ListingModerationController extends Controller
{
    public function __construct(private Moderation $service, private ListingChecks $checks)
    {
    }

    public function index(): View
    {
        Gate::authorize('moderate-listings');

        return view('admin.moderasi.index');
    }

    public function show(string $jenis, int $id): View
    {
        Gate::authorize('moderate-listings');

        $type = Moderation::type($jenis);
        $isVenue = $jenis === 'venue';
        $item = $isVenue ? $this->service->venue($id) : $this->service->field($id);
        abort_if(! $item, 404);

        $checks = $isVenue ? $this->checks->forVenue($item) : $this->checks->forField($item);

        return view('admin.moderasi.show', [
            'jenis'          => $jenis,
            'type'           => $type,
            'item'           => $item,
            'status'         => $item->{$type['status']},
            'checks'         => $checks,
            'blocked'        => $this->checks->isBlocked($checks),
            'history'        => $this->service->history($jenis, $id),
            'checklistItems' => Moderation::CHECKLIST[$jenis],
            'children'       => $isVenue ? $this->service->venueFields($id) : collect(),
            'photos'         => $isVenue ? collect() : $this->service->fieldPhotos($id),
            'facilities'     => $isVenue ? collect() : $this->service->fieldFacilities($id),
            'schedule'       => $isVenue ? ['rows' => collect(), 'note' => null] : $this->service->fieldSchedule($id),
        ]);
    }

    public function approve(ApproveListingRequest $request, string $jenis, int $id): RedirectResponse
    {
        try {
            $name = $this->service->approve($jenis, $id, $request->user(), $request->validated('checklist'));
        } catch (ListingAlreadyProcessedException $e) {
            return $this->backToList($jenis)->with('error', $e->getMessage());
        } catch (ListingBlockedByChecksException $e) {
            return redirect()->route('admin.moderasi.show', ['jenis' => $jenis, 'id' => $id])->with('error', $e->getMessage());
        }

        return $this->backToList($jenis)->with('success', Moderation::type($jenis)['label'] . " {$name} disetujui.");
    }

    public function reject(RejectListingRequest $request, string $jenis, int $id): RedirectResponse
    {
        try {
            $name = $this->service->reject($jenis, $id, $request->user(), $request->validated('alasan_penolakan'));
        } catch (ListingAlreadyProcessedException $e) {
            return $this->backToList($jenis)->with('error', $e->getMessage());
        }

        return $this->backToList($jenis)->with('success', Moderation::type($jenis)['label'] . " {$name} ditolak.");
    }

    private function backToList(string $jenis): RedirectResponse
    {
        return redirect()->route('admin.moderasi.index', ['jenis' => $jenis]);
    }
}