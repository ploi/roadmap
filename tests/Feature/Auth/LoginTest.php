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
