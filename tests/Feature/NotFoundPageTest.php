<?php

it('renders the not found page with a csrf token for livewire on an unknown url', function () {
    $response = $this->get('/this-page-does-not-exist');

    $response->assertNotFound();
    expect($response->getContent())->toMatch('/"csrf":"[A-Za-z0-9]{40}"/');
});
