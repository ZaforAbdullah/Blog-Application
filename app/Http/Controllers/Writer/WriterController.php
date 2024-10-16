<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\ShowCommentsPostRequest;
use App\Http\Requests\UpdateWriterPostRequest;
use App\Http\Requests\Writer\EditPostRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class WriterController extends Controller
{
    public function showDashboard()
    {
        return view('writer.index');
    }

    /**
     * show new post form
     */
    public function newPost()
    {
        $categories = Category::all();
        return view('writer.create', compact('categories'));
    }

    public function storePost(CreatePostRequest $request)
    {


        try {
            DB::beginTransaction();

            /**
             * Tags
             */
            $tags = $request->tags;
            $tag_ids = [];
            foreach ($tags as $tag) {
                $tag_ids[] = Tag::query()
                                ->where(Tag::col_slug, SLUG($tag))
                                ->orWhere(Tag::col_title, $tag)
                                ->firstOrCreate([
                                    Tag::col_slug  => SLUG($tag),
                                    Tag::col_title => $tag
                                ]);
            }

            /**
             * categories
             */
            $cats = $request->categories;
            $cat_ids = [];
            foreach ($cats as $cat) {
                $cat_ids[] = Category::query()
                                     ->findOrFail($cat);
            }


            $post = Post::query()
                        ->create([
                            Post::col_writer_id => Auth::id(),
                            Post::col_title     => $request->title,
                            Post::col_slug      => SLUG(rand(0, 3) . $request->title),
                            Post::col_cover     => $request->cover,
                            Post::col_body      => $request->body,
                        ]);


            $post->categories()
                 ->saveMany($cat_ids);


            $post->tags()
                 ->saveMany($tag_ids);

            DB::commit();

            return redirect()
                ->route('new.post.writer')
                ->with('msg', 'New Post Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()
                ->route('new.post.writer')
                ->with('msg', 'Fail To create New Post . Try again');

        }

    }

    /**
     * show all logedIn writer posts
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showPostsList()
    {
        //TODO : complete me=> writer can see just its posts
        $posts = Post::query()
                     ->where(Post::col_writer_id, Auth::id())
                     ->paginate(5);

        return view('writer.list', compact('posts'));
    }

    public function editWriterPost(EditPostRequest $request, Post $post)
    {
        $categories = Category::all();
        //TODO create request(policy) writer just can edit its posts
        return view('writer.editpost', compact('post', 'categories'));
    }

    /**
     * update a post
     */
    public function updateWriterPost(UpdateWriterPostRequest $request, Post $post)
    {

        try {
            DB::beginTransaction();

            //update post
            $post->update($request->only(['title', 'slug', 'cover', 'body']));

            $cat_id_sync = [];

            //sync post_category table
            $post->categories()
                 ->sync($request->categories);

            //Saving ID tags and finally syncing the IDs in this array
            $tag_ids = [];

            //For the tag, we first check whether the tags exist in the tag argument or not
            //If it does not exist, a new tag will be created in the tag table
            //If there is, we save only the id of that tag from the tag table in the presentation
            foreach ($request->tags as $tag) {

                //check tag existence in tags table
                $is_tag_exist = Tag::query()
                                   ->where('title', $tag)
                                   ->orWhere('slug', SLUG($request->slug))
                                   ->get();
                //tag not exist
                if ($is_tag_exist->count() === 0) {
                    //create new tag
                    $tag_id = Tag::query()
                                 ->create([
                                     Tag::col_title => $tag,
                                     Tag::col_slug  => SLUG($tag)
                                 ]);

                    //The id of the new tag is stored in the tag table as a key value
                    $tag_ids[$tag_id->id] = $tag_id->id;

                } else {
                    //tag was exist in tags table

                    //Save the tag ID in the tag table as a key value
                    $tag_ids[$is_tag_exist[0]['id']] = $is_tag_exist[0]['id'];

                }
            }

            //syn post_tag table
            $post->tags()
                 ->sync($tag_ids);

            DB::commit();
            return redirect(route('list.post.writer'))->with('update-succ', 'post Updated Successfully');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect(route('list.post.writer'))->with('update-fail', 'Oops,we can not update the post,try a gain later');
        }


    }

    public function deletePost(DeletePostRequest $request, Post $post)
    {
        try {

            DB::beginTransaction();

            $post->tags()
                 ->detach();

            $post->categories()
                 ->detach();

            $post->delete();

            DB::commit();
            return redirect(route('list.post.writer'))->with('delete-succ', 'Post Deleted SuccessFully');

        } catch (\Exception $e) {
            Log::error($e);

            DB::rollBack();
        }
    }

    public function showPostComments(ShowCommentsPostRequest $request, Post $post)
    {
        $post = Post::with('comments.user')
                    ->where('id', $post->id)
                    ->get()
                    ->toArray();
        return view('writer.commentlist', compact('post'));
    }


    public function changeStateComment(Comment $comment)
    {

        $comment->update([
            Comment::col_show => $comment->show ? false : true
        ]);
        return redirect()
            ->back()
            ->with('state', 'comment show state changed');
    }


    public function getWriterPosts(User $user)
    {
        //check
        if (!$user->type === User::type_writer)
            abort(404);

        $posts = $user->posts()
                      ->paginate(2);


        return view('postlist', compact('user', 'posts'));
    }
}

