<?php

namespace App\Providers;

use App\Core\Contracts\CacheStore;
use App\Domains\Authentication\Events\AccountLocked;
use App\Domains\Authentication\Events\UserLoggedIn;
use App\Domains\Authentication\Events\UserLoggedOut;
use App\Domains\Authentication\Events\UserRegistered;
use App\Domains\Authentication\Listeners\BroadcastAuthenticationEvent;
use App\Domains\Authentication\Listeners\CreateNotifications;
use App\Domains\Authentication\Listeners\LogLogin;
use App\Domains\Authentication\Listeners\LogLogout;
use App\Domains\Authentication\Listeners\SendWelcomeEmail;
use App\Domains\Company\Events\BranchCreated;
use App\Domains\Company\Events\CertificationUpdated;
use App\Domains\Company\Events\CompanyUpdated;
use App\Domains\Company\Events\PartnerAdded;
use App\Domains\Company\Events\TestimonialPublished;
use App\Domains\Company\Listeners\BroadcastCompanyChanges;
use App\Domains\Company\Listeners\ClearCompanyCache;
use App\Domains\Company\Listeners\UpdateHomepageStatistics;
use App\Domains\Company\Policies\BranchPolicy;
use App\Domains\Company\Policies\CompanyContentPolicy;
use App\Domains\Company\Policies\CompanyPolicy;
use App\Domains\Company\Policies\PartnerPolicy;
use App\Domains\Company\Policies\TestimonialPolicy;
use App\Domains\Company\Support\ShareCompany;
use App\Domains\Configuration\Events\BrandingUpdated;
use App\Domains\Configuration\Events\FeatureDisabled;
use App\Domains\Configuration\Events\FeatureEnabled;
use App\Domains\Configuration\Events\NavigationUpdated;
use App\Domains\Configuration\Events\SettingsUpdated;
use App\Domains\Configuration\Listeners\BroadcastConfigurationChanged;
use App\Domains\Configuration\Listeners\ClearRedisConfigurationCache;
use App\Domains\Configuration\Listeners\LogConfigurationChange;
use App\Domains\Configuration\Policies\FeatureFlagPolicy;
use App\Domains\Configuration\Policies\NavigationPolicy;
use App\Domains\Configuration\Policies\SettingPolicy;
use App\Domains\Configuration\Support\ShareConfiguration;
use App\Domains\User\Policies\PermissionPolicy;
use App\Domains\User\Policies\RolePolicy;
use App\Domains\User\Policies\UserPolicy;
use App\Domains\Website\Livewire\AboutPage;
use App\Domains\Website\Livewire\ContactForm;
use App\Infrastructure\Cache\ApplicationCache;
use App\Models\Award;
use App\Models\Branch;
use App\Models\Certification;
use App\Models\Company;
use App\Models\CompanyStatistic;
use App\Models\Faq;
use App\Models\FeatureFlag;
use App\Models\LeadershipMember;
use App\Models\NavigationMenu;
use App\Models\Partner;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CacheStore::class, fn (): ApplicationCache => new ApplicationCache);
        $this->app->alias(CacheStore::class, ApplicationCache::class);
    }

    public function boot(): void
    {
        Livewire::component('website.contact-form', ContactForm::class);
        Livewire::component('website.about-page', AboutPage::class);

        $websiteViews = [
            'layouts.website',
            'components.layout.header',
            'components.layout.footer',
            'pages.home',
            'pages.about.index',
            'pages.contact.index',
        ];

        View::composer($websiteViews, ShareConfiguration::class);
        View::composer($websiteViews, ShareCompany::class);

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(NavigationMenu::class, NavigationPolicy::class);
        Gate::policy(FeatureFlag::class, FeatureFlagPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(Partner::class, PartnerPolicy::class);
        Gate::policy(Testimonial::class, TestimonialPolicy::class);
        Gate::policy(LeadershipMember::class, CompanyContentPolicy::class);
        Gate::policy(Certification::class, CompanyContentPolicy::class);
        Gate::policy(Award::class, CompanyContentPolicy::class);
        Gate::policy(Faq::class, CompanyContentPolicy::class);
        Gate::policy(CompanyStatistic::class, CompanyContentPolicy::class);

        Gate::define('viewPulse', function (?User $user = null): bool {
            return $this->app->environment('local') || $user !== null;
        });

        Event::listen(UserRegistered::class, SendWelcomeEmail::class);
        Event::listen(UserLoggedIn::class, LogLogin::class);
        Event::listen(UserLoggedIn::class, BroadcastAuthenticationEvent::class);
        Event::listen(UserLoggedOut::class, LogLogout::class);
        Event::listen(UserLoggedOut::class, BroadcastAuthenticationEvent::class);
        Event::listen(AccountLocked::class, BroadcastAuthenticationEvent::class);
        Event::listen(AccountLocked::class, CreateNotifications::class);

        $configurationEvents = [
            SettingsUpdated::class,
            BrandingUpdated::class,
            NavigationUpdated::class,
            FeatureEnabled::class,
            FeatureDisabled::class,
        ];

        foreach ($configurationEvents as $event) {
            Event::listen($event, ClearRedisConfigurationCache::class);
            Event::listen($event, LogConfigurationChange::class);
            Event::listen($event, BroadcastConfigurationChanged::class);
        }

        $companyEvents = [
            CompanyUpdated::class,
            BranchCreated::class,
            PartnerAdded::class,
            TestimonialPublished::class,
            CertificationUpdated::class,
        ];

        foreach ($companyEvents as $event) {
            Event::listen($event, ClearCompanyCache::class);
            Event::listen($event, BroadcastCompanyChanges::class);
        }

        Event::listen(CompanyUpdated::class, UpdateHomepageStatistics::class);
        Event::listen(TestimonialPublished::class, UpdateHomepageStatistics::class);
    }
}
