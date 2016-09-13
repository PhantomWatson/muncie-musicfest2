<?php
    echo $this->Form->input('name', ['label' => 'Your name']);
    echo $this->Form->input('jobs._ids', [
        'label' => 'Job(s) you\'d like to volunteer for',
        'options' => $jobs, 'multiple' => 'checkbox'
    ]);
    echo $this->Form->input('email', ['type' => 'email']);
    echo $this->Form->input('phone', ['placeholder' => '(###) ###-####', 'type' => 'tel']);
    echo $this->Form->input('availability', [
        'label' => [
            'text' => 'Availability on September 30<sup>th</sup>',
            'escape' => false
        ],
        'placeholder' => 'all day, from 2pm on, until 5pm, etc.'
    ]);
    $sizes = ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
    echo $this->Form->input('shirt_size', [
        'default' => 'M',
        'label' => 'T-Shirt size',
        'options' => array_combine($sizes, $sizes),
        'type' => 'select'
    ]);
    echo $this->Form->input('emergency_contact', [
        'label' => [
            'text' => 'Emergency contact name and phone number ' .
                '<span class="note">(in case we find you twitching in a corner)</span>',
            'escape' => false
        ],
        'placeholder' => 'e.g. Mom: (317) 545-5777'
    ]);
    echo $this->Form->input('message', [
        'label' => 'Thanks for signing up! Anything else you want to let us know?'
    ]);
?>
