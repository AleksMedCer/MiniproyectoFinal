<?php

use App\Mail\Codigo2FAMail;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

$prepararMailerProduccion = function (bool $validarCredenciales = false): void {
    if (! app()->environment('production')) {
        return;
    }

    if (in_array(config('mail.default'), ['log', 'array'], true)) {
        config(['mail.default' => 'smtp']);
    }

    if ((! config('mail.from.address') || config('mail.from.address') === 'hello@example.com') && config('mail.mailers.smtp.username')) {
        config(['mail.from.address' => config('mail.mailers.smtp.username')]);
    }

    if ($validarCredenciales && (! config('mail.mailers.smtp.username') || ! config('mail.mailers.smtp.password'))) {
        throw new RuntimeException('Configuracion SMTP incompleta: faltan MAIL_USERNAME o MAIL_PASSWORD.');
    }
};

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {email}', function (string $email) use ($prepararMailerProduccion) {
    $prepararMailerProduccion(true);

    Mail::to($email)->send(new Codigo2FAMail('123456'));

    $this->info("Correo de prueba enviado a {$email}.");
})->purpose('Enviar un correo de prueba usando la configuracion SMTP actual');

Artisan::command('mail:diagnose', function () use ($prepararMailerProduccion) {
    $prepararMailerProduccion();

    $this->line('MAIL_MAILER='.config('mail.default'));
    $this->line('MAIL_SCHEME='.(config('mail.mailers.smtp.scheme') ?: 'null'));
    $this->line('MAIL_HOST='.config('mail.mailers.smtp.host'));
    $this->line('MAIL_PORT='.config('mail.mailers.smtp.port'));
    $this->line('MAIL_USERNAME='.(config('mail.mailers.smtp.username') ? 'configurado' : 'faltante'));
    $this->line('MAIL_PASSWORD='.(config('mail.mailers.smtp.password') ? 'configurado' : 'faltante'));
    $this->line('MAIL_FROM_ADDRESS='.config('mail.from.address'));
    $this->line('MAIL_FROM_NAME='.config('mail.from.name'));
})->purpose('Mostrar diagnostico SMTP sin revelar secretos');

Artisan::command('demo:seed-users', function () {
    $telefonoDemo = env('DEMO_PHONE', '9617017722');

    $usuarios = [
        ['name' => 'Admin Master', 'email' => 'admin@netehis.com', 'role' => User::ROLE_ADMIN],
        ['name' => 'Medcer Prueba', 'email' => 'medcer94@gmail.com', 'role' => User::ROLE_ADMIN],
        ['name' => 'Gerente Ventas', 'email' => 'gerente@netehis.com', 'role' => User::ROLE_GERENTE],
        ['name' => 'TecnoNorte MX', 'email' => 'vendedor@netehis.com', 'role' => User::ROLE_VENDEDOR],
        ['name' => 'Comprador Demo', 'email' => 'comprador@netehis.com', 'role' => User::ROLE_COMPRADOR],
    ];

    foreach ($usuarios as $usuario) {
        User::query()->updateOrCreate(
            ['email' => $usuario['email']],
            [
                'name' => $usuario['name'],
                'phone' => $telefonoDemo,
                'password' => Hash::make('password'),
                'role' => $usuario['role'],
            ]
        );
    }

    collect(range(1, 10))->each(fn (int $numero) => User::query()->updateOrCreate(
        ['email' => sprintf('comprador.demo.%02d@netehis.com', $numero)],
        [
            'name' => sprintf('Comprador Demo %02d', $numero),
            'phone' => $telefonoDemo,
            'password' => Hash::make('password'),
            'role' => User::ROLE_COMPRADOR,
        ]
    ));

    $this->info('Usuarios demo asegurados.');
    $this->line('Total usuarios: '.User::query()->count());
    $this->line('Admins: '.User::query()->where('role', User::ROLE_ADMIN)->count());
    $this->line('Vendedores: '.User::query()->where('role', User::ROLE_VENDEDOR)->count());
    $this->line('Compradores: '.User::query()->where('role', User::ROLE_COMPRADOR)->count());
})->purpose('Crear o actualizar usuarios demo necesarios para pruebas y dashboard');
