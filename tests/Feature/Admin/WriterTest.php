<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WriterTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * Admin has root access
     *
     * @return void
     */
    public function test_admin_can_see_create_writer_form()
    {
        $admin = User::factory()
                     ->admin()
                     ->create();

        $this->actingAs($admin);

        $respo = $this->get(route('new.writer.admin'));

        $respo->assertOk();

    }

    /**
     * Non-admin should not have root access
     */
    public function test_only_admin_able_to_see_create_wirter_form()
    {
        $this->makeUser();

        $response = $this->get(route('new.writer.admin'));

        $response->assertForbidden();
    }


    /**
     * Validation error test
     */
    public function test_create_writer_validation_error()
    {
        $this->makeAdmin();

        $this->post(route('store.writer.admin'))
             ->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /**
     * Creating a new author by admin
     */
    public function test_create_new_writer()
    {
        $this->makeAdmin();

        $data = [
            'name'                  => 'test',
            'email'                 => 'adminali@ab.cc',
            'password'              => '12345',
            'password_confirmation' => '12345'

        ];

        $res = $this->post(route('store.writer.admin'), $data);

        $this->assertDatabaseHas('users', [
            'name'  => 'test',
            'email' => 'adminali@ab.cc',
            'type'  => 'writer'
        ]);

        $res->assertRedirect(route('new.writer.admin'))
            ->assertSessionHas('success', 'new Writer Created Successfully');

    }

    public function test_writer_list()
    {
        $this->makeAdmin();
        $writers = User::factory()
                       ->count(4)
                       ->writer()
                       ->create();

        $resp = $this->get(route('list.writer.admin'));

        $resp->assertViewIs('admin.writer.list');

        $resp->assertViewHas('writers',
            User::where('type', 'writer')
                ->paginate(2));
    }


    public function test_show_writer_posts()
    {
        $this->makeAdmin();

        //create writer with its posts
        $writer = User::factory()
                      ->writer()
                      ->hasPosts(3)
                      ->create();

        //send get request
        $resp = $this->get(route('posts.writer.admin', $writer));

        //assert view name
        $resp->assertViewIs('admin.writer.writerpostlists');

        //assert data in view
        $resp->assertViewHas('writer',$writer);


    }

    /**
     * A simple user login
     */
    private function makeUser(): void
    {
        //normall user
        $user = User::factory()
                    ->user()
                    ->create();
        //login
        $this->actingAs($user);
    }

    /**
     * An admin login
     */
    private function makeAdmin(): void
    {
        // admin user
        $user = User::factory()
                    ->admin()
                    ->create();
        //login
        $this->actingAs($user);
    }
}
