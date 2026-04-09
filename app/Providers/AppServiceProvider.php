<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\RolesPermissions\RoleRepositoryInterface;
use App\Repositories\Eloquent\RolesPermissions\RoleRepository;
use App\Repositories\Interfaces\VolunteerCertificateRepositoryInterface;
use App\Repositories\Eloquent\VolunteerCertificateRepository;
use App\Repositories\Interfaces\RolesPermissions\RolePermissionRepositoryInterface;
use App\Repositories\Eloquent\RolesPermissions\RolePermissionRepository;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Eloquent\DocumentRepository;
use App\Repositories\Interfaces\EvaluationRepositoryInterface;
use App\Repositories\Eloquent\EvaluationRepository;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Eloquent\TaskRepository;
use App\Repositories\Interfaces\ApplicationRepositoryInterface;
use App\Repositories\Eloquent\ApplicationRepository;
use App\Repositories\Eloquent\Auth\AuthRepository;
use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Repositories\Eloquent\LocationRepository;
use App\Repositories\Interfaces\OpportunityRepositoryInterface;
use App\Repositories\Eloquent\OpportunityRepository;
use App\Repositories\Interfaces\BadgeRepositoryInterface;
use App\Repositories\Eloquent\BadgeRepository;
use App\Repositories\Interfaces\CertificateRepositoryInterface;
use App\Repositories\Eloquent\CertificateRepository;
use App\Repositories\Interfaces\VolunteerRepositoryInterface;
use App\Repositories\Eloquent\VolunteerRepository;
use App\Repositories\Interfaces\SkillRepositoryInterface;
use App\Repositories\Eloquent\SkillRepository;
use App\Repositories\Interfaces\OrganizationProfileRepositoryInterface;
use App\Repositories\Eloquent\OrganizationProfileRepository;
use App\Repositories\Interfaces\ProfileRepositoryInterface;
use App\Repositories\Eloquent\ProfileRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Interfaces\Auth\AuthRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(VolunteerCertificateRepositoryInterface::class, VolunteerCertificateRepository::class);
        $this->app->bind(RolePermissionRepositoryInterface::class, RolePermissionRepository::class);
        // $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(EvaluationRepositoryInterface::class, EvaluationRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(ApplicationRepositoryInterface::class, ApplicationRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(OpportunityRepositoryInterface::class, OpportunityRepository::class);
        $this->app->bind(BadgeRepositoryInterface::class, BadgeRepository::class);
        $this->app->bind(CertificateRepositoryInterface::class, CertificateRepository::class);
        $this->app->bind(VolunteerRepositoryInterface::class, VolunteerRepository::class);
        $this->app->bind(SkillRepositoryInterface::class, SkillRepository::class);
        $this->app->bind(OrganizationProfileRepositoryInterface::class, OrganizationProfileRepository::class);
        $this->app->bind(ProfileRepositoryInterface::class, ProfileRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
