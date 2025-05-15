<?php

namespace App\Imports;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class BlogsImport implements OnEachRow, WithHeadingRow, WithValidation
{
    use Importable;

    public function onRow(Row $row)
    {
        $blog = Blog::create([
            'title' => $row['title'],
            'sub_title' => $row['sub_title'],
            'content' => $row['content'],
            'created_by' => auth()->user()->id,
        ]);

        $tags = explode("|", $row['tags']);
        $categories = explode("|", $row['categories']);

        foreach ($tags as $tag) {
            $modelTag = Tag::firstOrCreate([
                'name' => $tag,
                'type' => 'blog'
            ]);

            $blog->tags()->attach($modelTag->id);
        }

        foreach ($categories as $category) {
            $modelCategory = Category::firstOrCreate([
                'name' => $category,
                'type' => 'blog'
            ]);

            $blog->categories()->attach($modelCategory->id);
        }
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string'],
            'sub_title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'categories' => ['required', 'string'],
            'tags' => ['required', 'string'],
        ];

        return $rules;
    }
}
