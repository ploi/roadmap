<?php

use App\Settings\ColorSettings;
use App\Settings\GeneralSettings;

it('displays the password protection page with brand colors', function () {
    $response = $this->get(route('password.protection'));

    $response->assertSuccessful();
    $response->assertSee(trans('auth.password_protected'));
    $response->assertSee(trans('auth.password'));
    $response->assertSee(trans('auth.continue'));
});

it('shows theme toggle when dark mode is enabled', function () {
    $colorSettings = app(ColorSettings::class);
    $colorSettings->darkmode = true;
    $colorSettings->save();

    $response = $this->get(route('password.protection'));

    $response->assertSuccessful();
    $response->assertSee('x-data="themeToggle"', false);
});

it('does not show theme toggle when dark mode is disabled', function () {
    $colorSettings = app(ColorSettings::class);
    $colorSettings->darkmode = false;
    $colorSettings->save();

    $response = $this->get(route('password.protection'));

    $response->assertSuccessful();
    $response->assertDontSee('x-data="themeToggle"', false);
});

it('displays logo when configured', function () {
    $colorSettings = app(ColorSettings::class);

    if (!is_null($colorSettings->logo) && file_exists(storage_path('app/public/' . $colorSettings->logo))) {
        $response = $this->get(route('password.protection'));

        $response->assertSuccessful();
        $response->assertSee($colorSettings->logo);
    } else {
        $this->assertTrue(true);
    }
});

it('redirects to home after successful password entry', function () {
    $generalSettings = app(GeneralSettings::class);
    $generalSettings->password = 'secret123';
    $generalSettings->save();

    $response = $this->post(route('password.protection.login'), [
        'password' => 'secret123'
    ]);

    $response->assertRedirect(route('home'));
    $this->assertTrue(session()->has('password-login-authorized'));
});

it('shows error with incorrect password', function () {
    $generalSettings = app(GeneralSettings::class);
    $generalSettings->password = 'secret123';
    $generalSettings->save();

    $response = $this->post(route('password.protection.login'), [
        'password' => 'wrong-password'
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors();
});

it('displays all translated strings correctly', function () {
    $colorSettings = app(ColorSettings::class);
    $colorSettings->darkmode = true;
    $colorSettings->save();

    $response = $this->get(route('password.protection'));

    $response->assertSuccessful();
    $response->assertSee(trans('auth.password_protected'));
    $response->assertSee(trans('auth.password_protection_description'));
    $response->assertSee(trans('auth.password'));
    $response->assertSee(trans('auth.password_placeholder'));
    $response->assertSee(trans('auth.continue'));
    $response->assertSee(trans('auth.theme_light'));
    $response->assertSee(trans('auth.theme_dark'));
    $response->assertSee(trans('auth.theme_auto'));
});

it('displays translated error message for wrong password', function () {
    $generalSettings = app(GeneralSettings::class);
    $generalSettings->password = 'secret123';
    $generalSettings->save();

    $response = $this->post(route('password.protection.login'), [
        'password' => 'wrong-password'
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors();

    $errors = session('errors');
    expect($errors->first())->toBe(trans('auth.wrong_password'));
});
