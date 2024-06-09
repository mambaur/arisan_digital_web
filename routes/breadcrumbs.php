<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/**
 * Home
 * 
 * ---------------------------------------------------------------------------
 */

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

/**
 * Profile
 * 
 * ---------------------------------------------------------------------------
 */

// Home > Profile
Breadcrumbs::for('profile', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Profile', route('profile'));
});

/**
 * Groups
 * 
 * ---------------------------------------------------------------------------
 */

// Home > Groups
Breadcrumbs::for('groups', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Groups', route('groups'));
});

// Home > Groups > Create
Breadcrumbs::for('group/create', function (BreadcrumbTrail $trail) {
    $trail->parent('groups');
    $trail->push('Create', route('group_create'));
});

// Home > Groups > [Edit]
Breadcrumbs::for('group/edit', function (BreadcrumbTrail $trail, $resource) {
    $trail->parent('groups');
    $trail->push(@$resource->name ?? '', route('group_edit', $resource->id));
});

/**
 * Members
 * 
 * ---------------------------------------------------------------------------
 */

// Home > Members
Breadcrumbs::for('members', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Members', route('members'));
});

/**
 * Users
 * 
 * ---------------------------------------------------------------------------
 */

// Home > Users
Breadcrumbs::for('users', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Users', route('users'));
});
