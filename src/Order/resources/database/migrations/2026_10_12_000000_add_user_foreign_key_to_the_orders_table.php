<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $usersTable = $this->usersTable();
        if (!Schema::hasTable($usersTable) || $this->hasUserForeignKey()) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) use ($usersTable) {
            $table->foreign('user_id')->references('id')->on($usersTable);
        });
    }

    public function down(): void
    {
    }

    private function usersTable(): string
    {
        $userModel = config('auth.providers.users.model');

        return $userModel ? (new $userModel())->getTable() : 'users';
    }

    private function hasUserForeignKey(): bool
    {
        foreach (Schema::getForeignKeys('orders') as $foreignKey) {
            if ($foreignKey['columns'] === ['user_id']) {
                return true;
            }
        }

        return false;
    }
};
