<?php

declare(strict_types=1);

namespace Shopper\Livewire\Pages\Settings;

use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Shopper\Core\Models\Setting;

#[Layout('shopper::components.layouts.setting')]
class Analytics extends Component
{
    use WithFileUploads;

    public $google_analytics_tracking_id;

    public $google_analytics_view_id;

    public $google_analytics_add_js;

    public $google_tag_manager_account_id;

    public $facebook_pixel_account_id;

    public $json_file;

    public bool $credentials_json = false;

    public function mount(): void
    {
        /** @var Setting $ga_add_js */
        $ga_add_js = Setting::query()->where('key', 'google_analytics_add_js')->first();
        $this->google_analytics_tracking_id = env('ANALYTICS_TRACKING_ID');
        $this->google_analytics_view_id = env('ANALYTICS_VIEW_ID');
        $this->google_analytics_add_js = $ga_add_js->value ?? null;
        $this->google_tag_manager_account_id = env('GOOGLE_TAG_MANAGER_ACCOUNT_ID');
        $this->facebook_pixel_account_id = env('FACEBOOK_PIXEL_ACCOUNT_ID');
        $this->credentials_json = File::exists(storage_path('app/analytics/service-account-credentials.json'));
    }

    public function store(): void
    {
        $data = [
            'analytics_tracking_id' => $this->google_analytics_tracking_id,
            'analytics_view_id' => $this->google_analytics_view_id,
            'google_tag_manager_account_id' => $this->google_tag_manager_account_id,
            'facebook_pixel_account_id' => $this->facebook_pixel_account_id,
        ];

        Artisan::call('config:clear');

        foreach ($data as $key => $value) {
            file_put_contents(app()->environmentFilePath(), str_replace(
                mb_strtoupper($key) . '=' . env($value),
                mb_strtoupper($key) . '=' . $value,
                file_get_contents(app()->environmentFilePath())
            ));
        }

        Setting::query()->updateOrCreate(['key' => 'google_analytics_add_js'], [
            'key' => 'google_analytics_add_js',
            'value' => $this->google_analytics_add_js,
            'display_name' => Setting::lockedAttributesDisplayName('google_analytics_add_js'),
            'locked' => true,
        ]);

        $this->json_file?->storeAs('analytics', 'service-account-credentials.json');

        if (app()->environment('production')) {
            Artisan::call('config:cache');
        }

        Notification::make()
            ->body(__('shopper::notifications.analytics'))
            ->success()
            ->send();
    }

    public function downloadJson(): string
    {
        return Storage::url('/app/analytics/service-account-credentials.json');
    }

    public function render(): View
    {
        return view('shopper::livewire.pages.settings.analytics');
    }
}
