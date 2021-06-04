<?php

declare(strict_types=1);

/**
 * Laravel Commentable Package by Babak Hakimi.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Kalnoy\Nestedset\NestedSet;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->text('body');
            $table->morphs('commentable');
            $table->morphs('creator');
            NestedSet::columns($table);
            $table->boolean('approved')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
