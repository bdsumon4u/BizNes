<?php

use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;

beforeEach(function () {
    Filament::setCurrentPanel(
        Filament::getPanel('admin'),
    );
});

it('has only one user', function () {
    expect(UserResource::getEloquentQuery()->count())->toBeOne();
});

test('he has no business', function () {
    expect(UserResource::getEloquentQuery()->first()->businesses)->toBeEmpty();
});

test('he can create a business', function () {
    $user = UserResource::getEloquentQuery()->first();

    $user->businesses()->create([
        'name' => 'My Business',
    ]);

    expect($user->businesses)->toHaveCount(1);
});

test('he can create a business and assign it to another user', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => 'another@another.com',
        'password' => 'password',
    ]);
    $user->businesses()->create([
        'name' => 'My Business',
        'user_id' => $anotherUser->id,
    ]);
    expect($user->businesses)->toHaveCount(1);
    expect($anotherUser->businesses)->toHaveCount(1);
});

test('he can create a business and assign it to another user and the other user is the owner', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $user->businesses()->create([
        'name' => 'My Business',
        'user_id' => $anotherUser->id,
    ]);
    expect($user->businesses)->toHaveCount(1);
    expect($anotherUser->businesses)->toHaveCount(1);
    expect($anotherUser->businesses->first()->pivot->is_owner)->toBeTrue();
    expect($user->businesses->first()->pivot->is_owner)->toBeFalse();
});

test('he can create a business and assign it to another user and the other user is the owner and the user is the owner of the business', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
        'user_id' => $anotherUser->id,
    ]);
    $business->users()->attach($user->id, ['is_owner' => true]);
    expect($user->businesses)->toHaveCount(1);
    expect($anotherUser->businesses)->toHaveCount(1);
    expect($anotherUser->businesses->first()->pivot->is_owner)->toBeTrue();
    expect($user->businesses->first()->pivot->is_owner)->toBeTrue();
});

test('he can create a business and assign it to another user and the other user is the owner and the user is the owner of the business and the user is the owner of the business', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
        'user_id' => $anotherUser->id,
    ]);
    $business->users()->attach($user->id, ['is_owner' => true]);
    $anotherUser->businesses()->updateExistingPivot($business->id, ['is_owner' => true]);
    expect($user->businesses)->toHaveCount(1);
    expect($anotherUser->businesses)->toHaveCount(1);
    expect($anotherUser->businesses->first()->pivot->is_owner)->toBeTrue();
    expect($user->businesses->first()->pivot->is_owner)->toBeTrue();
});

test('he can add a user to a business', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
    ]);
    $business->users()->attach($anotherUser->id);
    expect($business->users)->toHaveCount(2);
});

test('user can add a user to the business but not as owner', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
    ]);
    $business->users()->attach($anotherUser->id, ['is_owner' => true]);
    expect($business->users)->toHaveCount(2);
    expect($business->users->first()->pivot->is_owner)->toBeTrue();
    expect($business->users->last()->pivot->is_owner)->toBeFalse();
});

test('owner can add a user to the business as owner', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
    ]);
    $business->users()->attach($anotherUser->id, ['is_owner' => true]);
    $business->users()->updateExistingPivot($anotherUser->id, ['is_owner' => true]);
    expect($business->users)->toHaveCount(2);
    expect($business->users->first()->pivot->is_owner)->toBeTrue();
    expect($business->users->last()->pivot->is_owner)->toBeTrue();
});

test('owner can remove a user from the business', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
    ]);
    $business->users()->attach($anotherUser->id, ['is_owner' => true]);
    $business->users()->detach($anotherUser->id);
    expect($business->users)->toHaveCount(1);
});

test('added user should accept the invitation', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
    ]);
    $business->users()->attach($anotherUser->id, ['is_owner' => true]);
    $anotherUser->businesses()->updateExistingPivot($business->id, ['is_owner' => true]);
    expect($anotherUser->businesses->first()->pivot->is_owner)->toBeTrue();
    expect($anotherUser->businesses->first()->pivot->is_accepted)->toBeFalse();
    $anotherUser->businesses()->updateExistingPivot($business->id, ['is_accepted' => true]);
    expect($anotherUser->businesses->first()->pivot->is_accepted)->toBeTrue();
});

test('otherwise the user should not be able to access the business', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
    ]);
    $business->users()->attach($anotherUser->id, ['is_owner' => true]);
    $anotherUser->businesses()->updateExistingPivot($business->id, ['is_owner' => true]);
    $anotherUser->businesses()->updateExistingPivot($business->id, ['is_accepted' => false]);
    expect($anotherUser->businesses->first()->pivot->is_owner)->toBeTrue();
    expect($anotherUser->businesses->first()->pivot->is_accepted)->toBeFalse();
    $this->actingAs($anotherUser);
    $this->get(route('businesses.show', $business->id))->assertForbidden();
});

test('and nobody can edit the user\'s name, email, etc.', function () {
    $user = UserResource::getEloquentQuery()->first();

    $anotherUser = UserResource::getEloquentQuery()->create([
        'name' => 'Another User',
        'email' => fake()->email(),
        'password' => 'password',
    ]);
    $business = $user->businesses()->create([
        'name' => 'My Business',
    ]);
    $business->users()->attach($anotherUser->id, ['is_owner' => true]);
    $anotherUser->businesses()->updateExistingPivot($business->id, ['is_owner' => true]);
    $anotherUser->businesses()->updateExistingPivot($business->id, ['is_accepted' => true]);
    $this->actingAs($anotherUser);
    $this->get(route('businesses.show', $business->id))->assertOk();
    $this->get(route('businesses.edit', $business->id))->assertForbidden();
    $this->put(route('businesses.update', $business->id), [
        'name' => 'New Name',
    ])->assertForbidden();
    $this->delete(route('businesses.destroy', $business->id))->assertForbidden();
});

/**
 * Authorized user sends invitation to another user (if exists) to join the business
 * If not exists, create a new user without password and send invitation
 * If invitation is accepted, the user can access the business
 * If invitation is not accepted, the user cannot access the business
 * If invitation is not accepted, nobody can edit the user's name, email, etc.
 * Authorized user can remove pending people from the business
 *
 * Nobody can remove business owner from the business
 * Owner can remove other owners from the business but not himself
 * Nobody can edit owner's name, email, etc.
 * Owner can edit other owners' name, email, etc.
 * Can't edit own role (role management)
 * Can't change own role (user management)
 */
