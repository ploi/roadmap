<?php

test('The login screen can be rendered by guests', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);

    $response->assertViewIs('auth.login');
});

test('users get redirected away from login', function () {
    $user = createUser();

    $response = $this->actingAs($user)->get(route('login'));

    $response->assertStatus(302);
    $response->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

test('guests can login using the form', function () {
    $user = createUser();

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('home'));
    $response->assertStatus(302);
});

test('login rejects an external intended URL', function () {
    $user = createUser();

    $this->get(route('login', ['intended' => 'https://attacker.example/landing']))
        ->assertSessionMissing('url.intended');

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('home'));
});

test('login accepts a local intended path', function () {
    $user = createUser();

    $this->get(route('login', ['intended' => '/items/example']))
        ->assertSessionHas('url.intended', '/items/example');

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/items/example');
});

test('login accepts a same-origin intended URL', function () {
    $user = createUser();
    $intended = route('items.show', 'example');

    $this->get(route('login', ['intended' => $intended]))
        ->assertSessionHas('url.intended', $intended);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect($intended);
});

test('login rejects a protocol-relative intended URL', function () {
    $this->get(route('login', ['intended' => '//attacker.example/landing']))
        ->assertSessionMissing('url.intended');
});

test('users cannot authenticate with an incorrect password', function () {
    $user = createUser();

    $response = $this->from(route('login'))->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertStatus(302);
    $response->assertSessionHasErrors('email');

    $this->assertTrue(session()->hasOldInput('email'));
    $this->assertFalse(session()->hasOldInput('password'));
});
