<?php

use Illuminate\Support\Facades\Mail;
use Lettermint\Laravel\Transport\LettermintTransportFactory;

test('lettermint mailer is defined and resolves', function () {
    config(['services.lettermint.token' => 'test-token']);

    // Mail is faked globally in Pest.php, so resolve through the real manager.
    $transport = Mail::getFacadeRoot()->manager->mailer('lettermint')->getSymfonyTransport();

    expect($transport)->toBeInstanceOf(LettermintTransportFactory::class);
});
