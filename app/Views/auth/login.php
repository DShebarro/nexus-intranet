<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Nexus Intranet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
    <?= \App\Core\Csrf::meta() ?>
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg-base, #0a0a0f);font-family:Inter,sans-serif;">
    <div style="width:100%;max-width:420px;padding:24px;">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#6366f1,#818cf8);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                <span style="color:white;font-weight:800;font-size:18px;">N</span>
            </div>
            <h1 style="font-size:22px;font-weight:700;color:var(--text-primary, #f1f1f4);">Nexus Intranet</h1>
            <p style="font-size:13px;color:var(--text-muted, #71717a);margin-top:4px;">Faça login para continuar</p>
        </div>

        <div style="background:var(--bg-card, #111118);border:1px solid var(--border, #1e1e2a);border-radius:16px;padding:28px;">
            <?php if (!empty($error)): ?>
            <div style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.2);border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#fb7185;">
                <?= e($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <?= \App\Core\Csrf::field() ?>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--text-muted, #71717a);margin-bottom:6px;">E-mail</label>
                    <input type="email" name="email" value="<?= e($email ?? '') ?>" required
                           style="width:100%;background:var(--bg-elevated, #16161f);border:1px solid var(--border-strong, #2a2a3a);border-radius:10px;padding:10px 14px;color:var(--text-primary, #f1f1f4);font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--text-muted, #71717a);margin-bottom:6px;">Senha</label>
                    <input type="password" name="password" required
                           style="width:100%;background:var(--bg-elevated, #16161f);border:1px solid var(--border-strong, #2a2a3a);border-radius:10px;padding:10px 14px;color:var(--text-primary, #f1f1f4);font-size:14px;outline:none;box-sizing:border-box;">
                </div>
                <button type="submit"
                        style="width:100%;background:linear-gradient(135deg,#6366f1,#818cf8);color:white;border:none;border-radius:10px;padding:12px;font-size:14px;font-weight:600;cursor:pointer;">
                    Entrar
                </button>
            </form>
        </div>
    </div>
</body>
</html>
