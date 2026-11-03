<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('HYPESOLE:create-owner', function () {
    $ownerExists = \App\Models\User::whereHas('role', fn ($q) => $q->where('slug', 'owner'))->exists();
    if ($ownerExists) {
        $this->error('Owner sudah tersedia. Bootstrap ditutup.');
        return self::FAILURE;
    }

    $name = $this->ask('Nama Owner');
    $email = strtolower(trim($this->ask('Email Owner')));
    $password = $this->secret('Password (minimal 12 karakter)');
    $confirmation = $this->secret('Ulangi password');

    if (strlen($password) < 12 || $password !== $confirmation || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Data tidak valid atau password tidak cocok.');
        return self::FAILURE;
    }

    try {
        \Illuminate\Support\Facades\DB::transaction(function () use ($name, $email, $password) {
            $role = \App\Models\Role::firstOrCreate(
                ['slug' => 'owner'],
                ['name' => 'Owner / Super Admin', 'description' => 'Akses penuh ke seluruh fitur', 'is_system' => true]
            );
            $role = \App\Models\Role::whereKey($role->id)->lockForUpdate()->firstOrFail();
            if (\App\Models\User::where('role_id', $role->id)->lockForUpdate()->exists()) {
                throw new \RuntimeException('Owner sudah tersedia.');
            }
            \App\Models\User::create([
                'name' => trim($name), 'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'role_id' => $role->id, 'is_active' => true,
            ]);
        });
    } catch (\Throwable $e) {
        $this->error('Gagal membuat Owner: '.$e->getMessage());
        return self::FAILURE;
    }

    $this->info('Owner berhasil dibuat. Login melalui /admin/login.');
    return self::SUCCESS;
})->purpose('Membuat akun Owner pertama melalui terminal, bukan endpoint publik');
