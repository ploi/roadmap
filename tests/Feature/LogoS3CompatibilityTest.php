<?php

use App\Models\User;
use App\Settings\ColorSettings;
use Illuminate\Http\UploadedFile;
use function Pest\Laravel\actingAs;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    // Clear any existing logo
    app(ColorSettings::class)->logo = null;
    app(ColorSettings::class)->save();
});

test('logo renders correctly with local storage', function () {
    Storage::fake('public');

    $logo = UploadedFile::fake()->image('logo.png');
    Storage::disk('public')->put('logo-test.png', $logo->getContent());

    app(ColorSettings::class)->logo = 'logo-test.png';
    app(ColorSettings::class)->save();

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(Storage::disk('public')->url('logo-test.png'), false);
});

test('logo renders correctly with s3 storage', function () {
    Storage::fake('public');

    // Simulate S3 by using the fake storage
    $logo = UploadedFile::fake()->image('logo.png');
    Storage::disk('public')->put('logo-s3.png', $logo->getContent());

    app(ColorSettings::class)->logo = 'logo-s3.png';
    app(ColorSettings::class)->save();

    $response = $this->get('/');

    $response->assertSuccessful();
    // Verify the URL is generated via Storage::url() which works for both local and S3
    expect(Storage::disk('public')->exists('logo-s3.png'))->toBeTrue();
    $response->assertSee(Storage::disk('public')->url('logo-s3.png'), false);
});

test('logo url includes cache busting parameter', function () {
    Storage::fake('public');

    $logo = UploadedFile::fake()->image('logo.png');
    Storage::disk('public')->put('logo-test.png', $logo->getContent());

    app(ColorSettings::class)->logo = 'logo-test.png';
    app(ColorSettings::class)->save();

    $response = $this->get('/');

    $response->assertSuccessful();

    // Verify cache busting parameter is present
    $lastModified = Storage::disk('public')->lastModified('logo-test.png');
    $response->assertSee('?v=' . $lastModified, false);
});

test('page renders with app name when no logo exists', function () {
    app(ColorSettings::class)->logo = null;
    app(ColorSettings::class)->save();

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(config('app.name'));
});

test('page renders with app name when logo file does not exist', function () {
    app(ColorSettings::class)->logo = 'non-existent-logo.png';
    app(ColorSettings::class)->save();

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(config('app.name'));
});

test('favicon renders correctly with local storage', function () {
    Storage::fake('public');

    $favicon = UploadedFile::fake()->image('favicon.png');
    Storage::disk('public')->put('favicon.png', $favicon->getContent());

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(Storage::disk('public')->url('favicon.png'), false);
    $response->assertSee('rel="icon"', false);
});

test('favicon renders correctly with s3 storage', function () {
    Storage::fake('public');

    $favicon = UploadedFile::fake()->image('favicon.png');
    Storage::disk('public')->put('favicon.png', $favicon->getContent());

    $response = $this->get('/');

    $response->assertSuccessful();
    expect(Storage::disk('public')->exists('favicon.png'))->toBeTrue();
    $response->assertSee(Storage::disk('public')->url('favicon.png'), false);
});

test('favicon url includes cache busting parameter', function () {
    Storage::fake('public');

    $favicon = UploadedFile::fake()->image('favicon.png');
    Storage::disk('public')->put('favicon.png', $favicon->getContent());

    $response = $this->get('/');

    $response->assertSuccessful();

    // Verify cache busting parameter is present
    $lastModified = Storage::disk('public')->lastModified('favicon.png');
    $response->assertSee('?v=' . $lastModified, false);
});
