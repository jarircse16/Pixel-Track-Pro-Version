<?php
use PixelYourSite\GA\Helpers;


function getCompleteRegistrationEventParamsV4() {

    return array(
        'name' => 'sign_up',
        'data' => array(
            'content_name'    => get_the_title(),
            'event_url'       => \PixelYourSite\getCurrentPageUrl(true),
            'method'          => \PixelYourSite\getUserRoles(),
        ),
    );
}