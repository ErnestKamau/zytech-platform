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
use App\Domains\Client\Events\ClientArchived;
use App\Domains\Client\Events\ClientCreated;
use App\Domains\Client\Events\ClientUpdated;
use App\Domains\Client\Events\CommunicationLogged;
use App\Domains\Client\Events\DocumentUploaded;
use App\Domains\Client\Events\PortalAccessGranted;
use App\Domains\Client\Listeners\BroadcastClientUpdate;
use App\Domains\Client\Listeners\ClearClientCache;
use App\Domains\Client\Listeners\IndexClient;
use App\Domains\Client\Listeners\NotifyAssignedStaff;
use App\Domains\Client\Policies\ClientDocumentPolicy;
use App\Domains\Client\Policies\ClientNotePolicy;
use App\Domains\Client\Policies\ClientPolicy;
use App\Domains\Communication\Policies\AnnouncementPolicy as PlatformAnnouncementPolicy;
use App\Domains\Communication\Policies\NotificationTemplatePolicy;
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
use App\Domains\Knowledge\Events\ArticleArchived;
use App\Domains\Knowledge\Events\ArticleCreated;
use App\Domains\Knowledge\Events\ArticlePublished;
use App\Domains\Knowledge\Events\ArticleUpdated;
use App\Domains\Knowledge\Events\FeaturedArticleChanged;
use App\Domains\Knowledge\Listeners\BroadcastArticleChanges;
use App\Domains\Knowledge\Listeners\ClearArticleCache;
use App\Domains\Knowledge\Listeners\GenerateArticleSeo;
use App\Domains\Knowledge\Listeners\IndexArticle;
use App\Domains\Knowledge\Livewire\ArticleFaqs as KnowledgeArticleFaqs;
use App\Domains\Knowledge\Livewire\FeaturedArticles as FeaturedArticleComponents;
use App\Domains\Knowledge\Livewire\RelatedArticles as RelatedArticleComponents;
use App\Domains\Knowledge\Policies\ArticleAuthorPolicy;
use App\Domains\Knowledge\Policies\ArticleCategoryPolicy;
use App\Domains\Knowledge\Policies\ArticleContentPolicy;
use App\Domains\Knowledge\Policies\ArticlePolicy;
use App\Domains\Knowledge\Support\ShareKnowledge;
use App\Domains\Media\Events\MediaConverted;
use App\Domains\Media\Events\MediaDeleted;
use App\Domains\Media\Events\MediaMoved;
use App\Domains\Media\Events\MediaOptimized;
use App\Domains\Media\Events\MediaUploaded;
use App\Domains\Media\Listeners\BroadcastMediaUploaded;
use App\Domains\Media\Listeners\ClearMediaCache;
use App\Domains\Media\Listeners\IndexMedia;
use App\Domains\Media\Listeners\OptimizeUploadedImage;
use App\Domains\Media\Livewire\MediaPicker;
use App\Domains\Media\Policies\FolderPolicy;
use App\Domains\Media\Policies\MediaPolicy;
use App\Domains\Portal\Events\ClientLoggedIn;
use App\Domains\Portal\Events\MeetingCancelled;
use App\Domains\Portal\Events\MeetingScheduled;
use App\Domains\Portal\Events\MessageSent;
use App\Domains\Portal\Events\NotificationCreated;
use App\Domains\Portal\Events\PortalDocumentDownloaded;
use App\Domains\Portal\Events\TicketClosed;
use App\Domains\Portal\Events\TicketOpened;
use App\Domains\Portal\Listeners\BroadcastPortalUpdate;
use App\Domains\Portal\Listeners\ClearDashboardCache;
use App\Domains\Portal\Listeners\LogPortalActivity;
use App\Domains\Portal\Listeners\SendEmailNotification;
use App\Domains\Portal\Policies\AnnouncementPolicy as PortalAnnouncementPolicy;
use App\Domains\Portal\Policies\MeetingPolicy;
use App\Domains\Portal\Policies\MessagePolicy;
use App\Domains\Portal\Policies\SupportPolicy;
use App\Domains\Project\Events\FeaturedProjectChanged;
use App\Domains\Project\Events\ProjectArchived;
use App\Domains\Project\Events\ProjectCreated;
use App\Domains\Project\Events\ProjectPublished;
use App\Domains\Project\Events\ProjectUpdated;
use App\Domains\Project\Listeners\BroadcastProjectChanges;
use App\Domains\Project\Listeners\ClearProjectCache;
use App\Domains\Project\Listeners\GenerateProjectSeo;
use App\Domains\Project\Listeners\IndexProject;
use App\Domains\Project\Livewire\FeaturedProjects as FeaturedProjectComponents;
use App\Domains\Project\Livewire\RelatedProjects as RelatedProjectComponents;
use App\Domains\Project\Policies\ProjectCategoryPolicy;
use App\Domains\Project\Policies\ProjectContentPolicy;
use App\Domains\Project\Policies\ProjectPolicy;
use App\Domains\Project\Support\ShareProjects;
use App\Domains\Quotation\Events\LeadCreated;
use App\Domains\Quotation\Events\LeadQualified;
use App\Domains\Quotation\Events\QuotationAccepted;
use App\Domains\Quotation\Events\QuotationApproved;
use App\Domains\Quotation\Events\QuotationCreated;
use App\Domains\Quotation\Events\QuotationRejected;
use App\Domains\Quotation\Events\QuotationRequestSubmitted;
use App\Domains\Quotation\Events\QuotationSent;
use App\Domains\Quotation\Events\SiteVisitScheduled;
use App\Domains\Quotation\Listeners\BroadcastQuotationStatus;
use App\Domains\Quotation\Listeners\GenerateQuotationPdf;
use App\Domains\Quotation\Listeners\NotifySalesTeam;
use App\Domains\Quotation\Listeners\SendQuotationEmail;
use App\Domains\Quotation\Policies\LeadPolicy;
use App\Domains\Quotation\Policies\QuotationPolicy;
use App\Domains\Quotation\Policies\QuotationRequestPolicy;
use App\Domains\Quotation\Policies\SiteVisitPolicy;
use App\Domains\Search\Livewire\SearchPage;
use App\Domains\Seo\Listeners\ClearSitemapCache;
use App\Domains\Seo\Policies\SeoRedirectPolicy;
use App\Domains\Service\Events\ServiceArchived;
use App\Domains\Service\Events\ServiceCreated;
use App\Domains\Service\Events\ServicePublished;
use App\Domains\Service\Events\ServiceUpdated;
use App\Domains\Service\Listeners\BroadcastServiceChanges;
use App\Domains\Service\Listeners\ClearServiceCache;
use App\Domains\Service\Listeners\GenerateServiceSeo;
use App\Domains\Service\Listeners\IndexService;
use App\Domains\Service\Livewire\FeaturedServices;
use App\Domains\Service\Livewire\RelatedServices;
use App\Domains\Service\Livewire\ServiceFaqs;
use App\Domains\Service\Policies\ServiceCategoryPolicy;
use App\Domains\Service\Policies\ServiceContentPolicy;
use App\Domains\Service\Policies\ServicePolicy;
use App\Domains\Service\Support\ShareServices;
use App\Domains\User\Policies\PermissionPolicy;
use App\Domains\User\Policies\RolePolicy;
use App\Domains\User\Policies\UserPolicy;
use App\Domains\Website\Livewire\AboutPage;
use App\Domains\Website\Livewire\ArticleShowPage;
use App\Domains\Website\Livewire\ContactForm;
use App\Domains\Website\Livewire\DownloadsPage;
use App\Domains\Website\Livewire\KnowledgePage;
use App\Domains\Website\Livewire\ProjectShowPage;
use App\Domains\Website\Livewire\ProjectsPage;
use App\Domains\Website\Livewire\RequestQuotationForm;
use App\Domains\Website\Livewire\ServiceShowPage;
use App\Domains\Website\Livewire\ServicesPage;
use App\Domains\Website\Livewire\TrackQuotationPage;
use App\Infrastructure\Cache\ApplicationCache;
use App\Models\Announcement;
use App\Models\Article;
use App\Models\ArticleAuthor;
use App\Models\ArticleCategory;
use App\Models\ArticleDownload;
use App\Models\ArticleFaq;
use App\Models\ArticleSection;
use App\Models\ArticleTag;
use App\Models\Award;
use App\Models\Branch;
use App\Models\Certification;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\ClientGroup;
use App\Models\ClientNote;
use App\Models\ClientTag;
use App\Models\Company;
use App\Models\CompanyStatistic;
use App\Models\Faq;
use App\Models\FeatureFlag;
use App\Models\LeadershipMember;
use App\Models\LeadSource;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\MediaTag;
use App\Models\MeetingRequest;
use App\Models\NavigationMenu;
use App\Models\NotificationTemplate;
use App\Models\Partner;
use App\Models\Permission;
use App\Models\PortalAnnouncement;
use App\Models\PortalConversation;
use App\Models\Project;
use App\Models\ProjectBeforeAfter;
use App\Models\ProjectCategory;
use App\Models\ProjectGalleryItem;
use App\Models\ProjectMilestone;
use App\Models\ProjectProgressUpdate;
use App\Models\ProjectStatistic;
use App\Models\Quotation;
use App\Models\QuotationApproval;
use App\Models\QuotationDocument;
use App\Models\QuotationItem;
use App\Models\QuotationRequest;
use App\Models\QuotationRequestAttachment;
use App\Models\QuotationRevision;
use App\Models\QuotationSection;
use App\Models\Role;
use App\Models\SalesLead;
use App\Models\SeoRedirect;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceFaq;
use App\Models\ServiceFeature;
use App\Models\ServiceProcess;
use App\Models\ServiceRelatedProject;
use App\Models\ServiceStatistic;
use App\Models\Setting;
use App\Models\SiteVisit;
use App\Models\SupportTicket;
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
        Livewire::component('website.downloads-page', DownloadsPage::class);
        Livewire::component('website.projects-page', ProjectsPage::class);
        Livewire::component('website.project-show', ProjectShowPage::class);
        Livewire::component('project.featured-projects', FeaturedProjectComponents::class);
        Livewire::component('project.related-projects', RelatedProjectComponents::class);
        Livewire::component('website.services-page', ServicesPage::class);
        Livewire::component('website.service-show', ServiceShowPage::class);
        Livewire::component('service.featured-services', FeaturedServices::class);
        Livewire::component('service.related-services', RelatedServices::class);
        Livewire::component('service.faqs', ServiceFaqs::class);
        Livewire::component('website.knowledge-page', KnowledgePage::class);
        Livewire::component('website.article-show', ArticleShowPage::class);
        Livewire::component('knowledge.featured-articles', FeaturedArticleComponents::class);
        Livewire::component('knowledge.related-articles', RelatedArticleComponents::class);
        Livewire::component('knowledge.article-faqs', KnowledgeArticleFaqs::class);
        Livewire::component('website.request-quotation-form', RequestQuotationForm::class);
        Livewire::component('website.track-quotation', TrackQuotationPage::class);
        Livewire::component('website.search-page', SearchPage::class);
        Livewire::component('media.picker', MediaPicker::class);

        $websiteViews = [
            'layouts.website',
            'components.layout.header',
            'components.layout.footer',
            'pages.home',
            'pages.about.index',
            'pages.contact.index',
            'pages.search.index',
            'pages.downloads.index',
            'pages.legal.privacy',
            'pages.legal.terms',
            'pages.legal.careers',
            'errors.404',
            'pages.services.index',
            'pages.services.show',
            'pages.projects.index',
            'pages.projects.show',
            'pages.knowledge.index',
            'pages.knowledge.show',
            'pages.quote.index',
            'pages.quote.success',
            'pages.quote.track',
        ];

        View::composer($websiteViews, ShareConfiguration::class);
        View::composer($websiteViews, ShareCompany::class);
        View::composer(['pages.home', 'pages.services.index'], ShareServices::class);
        View::composer(['pages.home', 'pages.projects.index'], ShareProjects::class);
        View::composer(['pages.home', 'pages.knowledge.index'], ShareKnowledge::class);

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
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(MediaFolder::class, FolderPolicy::class);
        Gate::policy(MediaTag::class, MediaPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(ServiceCategory::class, ServiceCategoryPolicy::class);
        Gate::policy(ServiceFaq::class, ServiceContentPolicy::class);
        Gate::policy(ServiceFeature::class, ServiceContentPolicy::class);
        Gate::policy(ServiceProcess::class, ServiceContentPolicy::class);
        Gate::policy(ServiceStatistic::class, ServiceContentPolicy::class);
        Gate::policy(ServiceRelatedProject::class, ServiceContentPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(ProjectCategory::class, ProjectCategoryPolicy::class);
        Gate::policy(ProjectGalleryItem::class, ProjectContentPolicy::class);
        Gate::policy(ProjectMilestone::class, ProjectContentPolicy::class);
        Gate::policy(ProjectProgressUpdate::class, ProjectContentPolicy::class);
        Gate::policy(ProjectStatistic::class, ProjectContentPolicy::class);
        Gate::policy(ProjectBeforeAfter::class, ProjectContentPolicy::class);
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(ArticleCategory::class, ArticleCategoryPolicy::class);
        Gate::policy(ArticleAuthor::class, ArticleAuthorPolicy::class);
        Gate::policy(ArticleSection::class, ArticleContentPolicy::class);
        Gate::policy(ArticleFaq::class, ArticleContentPolicy::class);
        Gate::policy(ArticleDownload::class, ArticleContentPolicy::class);
        Gate::policy(ArticleTag::class, ArticleContentPolicy::class);
        Gate::policy(LeadSource::class, LeadPolicy::class);
        Gate::policy(SalesLead::class, LeadPolicy::class);
        Gate::policy(QuotationRequest::class, QuotationRequestPolicy::class);
        Gate::policy(QuotationRequestAttachment::class, QuotationRequestPolicy::class);
        Gate::policy(Quotation::class, QuotationPolicy::class);
        Gate::policy(QuotationSection::class, QuotationPolicy::class);
        Gate::policy(QuotationItem::class, QuotationPolicy::class);
        Gate::policy(QuotationRevision::class, QuotationPolicy::class);
        Gate::policy(QuotationApproval::class, QuotationPolicy::class);
        Gate::policy(QuotationDocument::class, QuotationPolicy::class);
        Gate::policy(SiteVisit::class, SiteVisitPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(ClientDocument::class, ClientDocumentPolicy::class);
        Gate::policy(ClientNote::class, ClientNotePolicy::class);
        Gate::policy(ClientTag::class, ClientPolicy::class);
        Gate::policy(ClientGroup::class, ClientPolicy::class);
        Gate::policy(PortalConversation::class, MessagePolicy::class);
        Gate::policy(SupportTicket::class, SupportPolicy::class);
        Gate::policy(MeetingRequest::class, MeetingPolicy::class);
        Gate::policy(PortalAnnouncement::class, PortalAnnouncementPolicy::class);
        Gate::policy(Announcement::class, PlatformAnnouncementPolicy::class);
        Gate::policy(NotificationTemplate::class, NotificationTemplatePolicy::class);
        Gate::policy(SeoRedirect::class, SeoRedirectPolicy::class);

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

        $mediaEvents = [
            MediaUploaded::class,
            MediaDeleted::class,
            MediaMoved::class,
            MediaConverted::class,
            MediaOptimized::class,
        ];

        foreach ($mediaEvents as $event) {
            Event::listen($event, ClearMediaCache::class);
        }

        Event::listen(MediaUploaded::class, BroadcastMediaUploaded::class);
        Event::listen(MediaDeleted::class, BroadcastMediaUploaded::class);
        Event::listen(MediaMoved::class, BroadcastMediaUploaded::class);
        Event::listen(MediaUploaded::class, OptimizeUploadedImage::class);
        Event::listen(MediaUploaded::class, IndexMedia::class);

        $serviceEvents = [
            ServiceCreated::class,
            ServicePublished::class,
            ServiceUpdated::class,
            ServiceArchived::class,
            FeaturedServiceChanged::class,
        ];

        foreach ($serviceEvents as $event) {
            Event::listen($event, ClearServiceCache::class);
            Event::listen($event, BroadcastServiceChanges::class);
            Event::listen($event, ClearSitemapCache::class);
        }

        Event::listen(ServiceCreated::class, GenerateServiceSeo::class);
        Event::listen(ServiceUpdated::class, GenerateServiceSeo::class);
        Event::listen(ServiceCreated::class, IndexService::class);
        Event::listen(ServicePublished::class, IndexService::class);
        Event::listen(ServiceUpdated::class, IndexService::class);

        $projectEvents = [
            ProjectCreated::class,
            ProjectPublished::class,
            ProjectUpdated::class,
            ProjectArchived::class,
            FeaturedProjectChanged::class,
        ];

        foreach ($projectEvents as $event) {
            Event::listen($event, ClearProjectCache::class);
            Event::listen($event, BroadcastProjectChanges::class);
            Event::listen($event, ClearSitemapCache::class);
        }

        Event::listen(ProjectCreated::class, GenerateProjectSeo::class);
        Event::listen(ProjectUpdated::class, GenerateProjectSeo::class);
        Event::listen(ProjectCreated::class, IndexProject::class);
        Event::listen(ProjectPublished::class, IndexProject::class);
        Event::listen(ProjectUpdated::class, IndexProject::class);

        $articleEvents = [
            ArticleCreated::class,
            ArticlePublished::class,
            ArticleUpdated::class,
            ArticleArchived::class,
            FeaturedArticleChanged::class,
        ];

        foreach ($articleEvents as $event) {
            Event::listen($event, ClearArticleCache::class);
            Event::listen($event, BroadcastArticleChanges::class);
            Event::listen($event, ClearSitemapCache::class);
        }

        Event::listen(ArticleCreated::class, GenerateArticleSeo::class);
        Event::listen(ArticleUpdated::class, GenerateArticleSeo::class);
        Event::listen(ArticleCreated::class, IndexArticle::class);
        Event::listen(ArticlePublished::class, IndexArticle::class);
        Event::listen(ArticleUpdated::class, IndexArticle::class);

        $quotationEvents = [
            QuotationRequestSubmitted::class,
            LeadCreated::class,
            LeadQualified::class,
            QuotationCreated::class,
            QuotationApproved::class,
            QuotationSent::class,
            QuotationAccepted::class,
            QuotationRejected::class,
            SiteVisitScheduled::class,
        ];

        foreach ($quotationEvents as $event) {
            Event::listen($event, NotifySalesTeam::class);
            Event::listen($event, BroadcastQuotationStatus::class);
        }

        Event::listen(QuotationRequestSubmitted::class, SendQuotationEmail::class);
        Event::listen(QuotationSent::class, SendQuotationEmail::class);
        Event::listen(QuotationApproved::class, GenerateQuotationPdf::class);
        Event::listen(QuotationSent::class, GenerateQuotationPdf::class);

        $clientEvents = [
            ClientCreated::class,
            ClientUpdated::class,
            ClientArchived::class,
            DocumentUploaded::class,
            CommunicationLogged::class,
            PortalAccessGranted::class,
        ];

        foreach ($clientEvents as $event) {
            Event::listen($event, ClearClientCache::class);
            Event::listen($event, BroadcastClientUpdate::class);
        }

        Event::listen(ClientCreated::class, NotifyAssignedStaff::class);
        Event::listen(ClientCreated::class, IndexClient::class);
        Event::listen(ClientUpdated::class, IndexClient::class);
        Event::listen(ClientArchived::class, IndexClient::class);

        $portalEvents = [
            MessageSent::class,
            TicketOpened::class,
            TicketClosed::class,
            MeetingScheduled::class,
            MeetingCancelled::class,
            NotificationCreated::class,
            PortalDocumentDownloaded::class,
        ];

        foreach ($portalEvents as $event) {
            Event::listen($event, BroadcastPortalUpdate::class);
            Event::listen($event, ClearDashboardCache::class);
        }

        Event::listen(MessageSent::class, SendEmailNotification::class);
        Event::listen(TicketOpened::class, SendEmailNotification::class);
        Event::listen(MeetingScheduled::class, SendEmailNotification::class);
        Event::listen(NotificationCreated::class, SendEmailNotification::class);

        Event::listen(ClientLoggedIn::class, LogPortalActivity::class);
        Event::listen(MessageSent::class, LogPortalActivity::class);
        Event::listen(TicketOpened::class, LogPortalActivity::class);
        Event::listen(TicketClosed::class, LogPortalActivity::class);
        Event::listen(MeetingScheduled::class, LogPortalActivity::class);
        Event::listen(MeetingCancelled::class, LogPortalActivity::class);
        Event::listen(PortalDocumentDownloaded::class, LogPortalActivity::class);
    }
}
