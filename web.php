<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function(){
    Route::get('/', 'index')->name('home.index');
    Route::get('/{tag:slug}/tag', 'showTag')->name('home.tag');
    Route::get('/{category:slug}/category', 'showCategory')->name('home.category');
    Route::get('/{user}/user', 'showUser')->name('home.user');
    Route::get('/{article:slug}/article', 'showArticle')->name('home.article');
});

Route::middleware('guest')->group(function(){
    Route::controller(AuthenticatedSessionController::class)->group(function(){
        Route::get('/register', 'create')->name('register.create');
        Route::post('/register', 'store')->name('register.store');
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware(['auth', 'verified', 'redirectUser']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/filter', [ArticleController::class, 'filterArticles'])->name('filter.articles');
    Route::get('/articles/search', [ArticleController::class, 'searchArticles'])->name('search.articles');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create')->can('create', Article::class);
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store')->can('create', Article::class);
    Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
    Route::get('/articles/{article:slug}/edit', [ArticleController::class, 'edit'])->name('articles.edit')->can('update', 'article');
    Route::put('/articles/{article:slug}', [ArticleController::class, 'update'])->name('articles.update')->can('update', 'article');
    Route::delete('/articles/{article:slug}', [ArticleController::class, 'destroy'])->name('articles.destroy')->can('delete', 'article');
    Route::delete('/articles/{article:slug}/image', [ArticleController::class, 'destroyImage'])->name('articles.destroyImage')->can('delete', 'article');
    Route::get('/articles/{tag:slug}/tag', [ArticleController::class, 'byTag'])->name('tag.articles');
    Route::get('/articles/{category:slug}/category', [ArticleController::class, 'byCategory'])->name('category.articles');
    Route::get('/articles/{user}/user', [ArticleController::class, 'byAuthor'])->name('user.articles');
    Route::get('/articles/{article:slug}/test', [ArticleController::class, 'testMail'])->name('test.mail');

    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::get('/comments/create', [CommentController::class, 'create'])->name('comments.create');
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{comment}', [CommentController::class, 'show'])->name('comments.show');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit')->can('update', 'comment');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update')->can('update', 'comment');

    Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create')->can('create', Tag::class);
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store')->can('create', Tag::class);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');
    Route::get('/tags/{tag:slug}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/tags/{tag:slug}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag:slug}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category:slug}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category:slug}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category:slug}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});

require __DIR__.'/auth.php';