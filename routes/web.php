<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AutheticateController;
use App\Http\Controllers\Guest\PostController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Writer\WriterController;
use Illuminate\Support\Facades\Route;
use UniSharp\LaravelFilemanager\Lfm;


Route::get('/', function () {
    return view('welcome');
});

/*
 |------------------------------
 | Auth routes
 |------------------------------
 */
Route::get('/login', [AutheticateController::class, 'showLoginForm'])
     ->name('show.login')
     ->middleware('guest');

Route::post('/login', [AutheticateController::class, 'login'])
     ->name('login');

Route::any('/logout', [AutheticateController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

//show register form
Route::get('/register', [AutheticateController::class, 'showRegisterForm'])
     ->name('register');

//regsiter normall user
Route::post('/register', [AutheticateController::class, 'register'])
     ->name('register.normall.user');

/*
 |------------------------------
 | admin routes
 |------------------------------
 */
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'can:admin']], function () {

    //Dashboard
    Route::get('/dashboard', [AdminController::class, 'showDashboard'])
         ->name('dashboard.admin');

    /*
     |------------------------------
     | for Writer
     |------------------------------
     |
     |
     |
     */
    //show create form
    Route::get('/writer/new', [AdminController::class, 'newWriterForm'])
         ->name('new.writer.admin');

    //store new writer
    Route::post('/writer/store', [AdminController::class, 'storeWriter'])
         ->name('store.writer.admin');

    //writer list
    Route::get('/writer/list', [AdminController::class, 'showWriterList'])
         ->name('list.writer.admin');

    //writer posts
    Route::get('/writer/posts/{user}', [AdminController::class, 'showWriterPosts'])
         ->name('posts.writer.admin');

    /*
     |------------------------------
     | for Category
     |------------------------------
     |
     |
     |
     */
    Route::get('/category/new', [AdminController::class, 'newCategory'])
         ->name('new.category.admin');
    Route::post('/category/store', [AdminController::class, 'storeNewCategory'])
         ->name('store.category.admin');

    Route::get('/category/list', [AdminController::class, 'showCategoryList'])
         ->name('list.category.admin');

    Route::get('/category/edit/{category}', [AdminController::class, 'editCategory'])
         ->name('edit.category.admin');

    Route::put('/category/update/{category}', [AdminController::class, 'updateCategory'])
         ->name('update.category.admin');
});

/*
 |------------------------------
 |  Writer Routes
 |------------------------------
 */
Route::group(['prefix' => 'writer', 'middleware' => ['auth']], function () {

    //Dashboard
    Route::get('/dashboard', [WriterController::class, 'showDashboard'])
         ->name('dashboard.writer');

    /*
     |------------------------------
     | for post
     |------------------------------
     */
    Route::get('/post/new', [WriterController::class, 'newPost'])
         ->name('new.post.writer');

    Route::post('/post/store', [WriterController::class, 'storePost'])
         ->name('store.post.writer');

    Route::get('/post/list', [WriterController::class, 'showPostsList'])
         ->name('list.post.writer');

    //edit
    Route::get('/post/edit/{post}', [WriterController::class, 'editWriterPost'])
         ->name('edit.post.writer');

    //update
    Route::put('/post/update/{post}', [WriterController::class, 'updateWriterPost'])
         ->name('update.post.writer');

    //delete
    Route::delete('/post/delete/{post}', [WriterController::class, 'deletePost'])
         ->name('delete.post.writer');

    /*
     |------------------------------
     | for comments
     |------------------------------
     |
     */
    Route::get('/post/comments/{post}', [WriterController::class, 'showPostComments'])
         ->name('comment.post.writer');

    Route::get('/post/{comment}', [WriterController::class, 'changeStateComment'])
         ->name('state.comments.writer');


});

/*
 |------------------------------
 | User Routes
 |------------------------------
 |
 |
 |
 */

Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {

    Route::post('/comment/add/{post}', [UserController::class, 'addComment'])
         ->name('add.comment.user');
});


/*
 |------------------------------
 | Guest Routes
 |------------------------------
 |
 |
 |
 */
Route::group([], function () {

    Route::get('/', [PostController::class, 'index'])
         ->name('index.guest');

    Route::get('/{post}/{slug}', [PostController::class, 'showSinglePost'])
         ->name('single.post.guest');


    //get writer posts by writer id
    Route::get('/writer/posts/{user}', [WriterController::class, 'getWriterPosts'])
         ->name('get.post.writer');

    //select posts by category
    Route::get('/post/categories/{category}', [PostController::class, 'getPostByCategories'])
         ->name('get.categories.post');

    //select posts by tags
    Route::get('/post/tags/{tag}', [PostController::class, 'getPostByTags'])
         ->name('get.tags.post');
});


/*
 |------------------------------
 | Laravel File Manager LMF
 |------------------------------
 |
 |
 |
 */
//TODO add middleware can:admin,writer
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    Lfm::routes();
});
