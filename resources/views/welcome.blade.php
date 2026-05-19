<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>AI Ответчик - Автоматические ответы для покупателей</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Минимальные стили для отображения */
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: system-ui, -apple-system, sans-serif; line-height: 1.5; background: #fff; }
            .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
            .hero { text-align: center; padding: 5rem 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
            .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; font-weight: 700; }
            .hero p { font-size: 1.35rem; opacity: 0.95; max-width: 700px; margin: 0 auto; }
            .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; padding: 4rem 2rem; }
            .feature-card { padding: 2.5rem; border-radius: 16px; background: #f8fafc; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s; }
            .feature-card:hover { transform: translateY(-5px); }
            .feature-card h3 { color: #667eea; margin-bottom: 1rem; font-size: 1.5rem; }
            .feature-card p { color: #4a5568; line-height: 1.7; }
            .how-it-works { padding: 5rem 2rem; background: #f8fafc; }
            .how-it-works h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #1a1a1a; }
            .steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; }
            .step { text-align: center; }
            .step-number { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; color: white; font-weight: bold; }
            .step h4 { font-size: 1.25rem; margin-bottom: 0.75rem; color: #1a1a1a; }
            .step p { color: #666; }
            .cta { text-align: center; padding: 5rem 2rem; background: white; }
            .cta h2 { font-size: 2.5rem; margin-bottom: 1rem; color: #1a1a1a; }
            .cta p { font-size: 1.25rem; color: #666; margin-bottom: 2.5rem; }
            .btn { display: inline-block; padding: 1rem 2.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; font-size: 1.1rem; }
            .btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4); }
            .btn-secondary { background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); margin-left: 1rem; }
            footer { background: #1a1a1a; color: white; padding: 2rem; text-align: center; }
            @media (max-width: 768px) {
                .hero h1 { font-size: 2.5rem; }
                .hero p { font-size: 1.1rem; }
                .features { grid-template-columns: 1fr; }
                .steps { grid-template-columns: 1fr; }
            }
        </style>
    @endif
</head>
<body>
    {{-- Hero Section --}}
    <section class="hero">
        <div class="container">
            <h1>🤖 AI Ответчик</h1>
            <p>Автоматический ответчик на комментарии и запросы покупателей с использованием искусственного интеллекта</p>
        </div>
    </section>
    
    {{-- Features Section --}}
    <section class="features">
        <div class="feature-card">
            <h3>⚡ Мгновенные ответы</h3>
            <p>Автоматически отвечайте на вопросы покупателей 24/7 без задержек. Увеличьте удовлетворенность клиентов и сократите время ответа до минимума.</p>
        </div>
        
        <div class="feature-card">
            <h3>🧠 Умный ИИ</h3>
            <p>Наша система на базе искусственного интеллекта понимает контекст, анализирует настроение и предоставляет персонализированные ответы для каждого клиента.</p>
        </div>
        
        <div class="feature-card">
            <h3>📊 Аналитика и отчеты</h3>
            <p>Получайте детальную статистику по обработанным запросам, анализируйте эффективность ответов и оптимизируйте работу службы поддержки.</p>
        </div>
        
        <div class="feature-card">
            <h3>🔗 Интеграции</h3>
            <p>Легко подключается к популярным платформам: социальные сети, мессенджеры, CRM-системы и маркетплейсы в несколько кликов.</p>
        </div>
        
        <div class="feature-card">
            <h3>🎯 Настройка под бизнес</h3>
            <p>Гибкие настройки тональности, стиля общения и правил обработки запросов. Адаптируйте ИИ под специфику вашего бизнеса.</p>
        </div>
        
        <div class="feature-card">
            <h3>🛡️ Безопасность данных</h3>
            <p>Ваши данные и данные клиентов защищены современными стандартами шифрования и соответствуют требованиям GDPR.</p>
        </div>
    </section>
    
    {{-- How It Works --}}
    <section class="how-it-works">
        <div class="container">
            <h2>Как это работает</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Подключение источников</h4>
                    <p>Подключите ваши каналы связи: соцсети, сайт, мессенджеры</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Настройка ИИ</h4>
                    <p>Обучите систему на ваших данных и настройте правила ответов</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Автоматическая работа</h4>
                    <p>ИИ обрабатывает запросы и отвечает клиентам автоматически</p>
                </div>
            </div>
        </div>
    </section>
    
    {{-- CTA Section --}}
    <section class="cta">
        <div class="container">
            <h2>Готовы автоматизировать поддержку?</h2>
            <p>Начните использовать AI Ответчик уже сегодня и сэкономьте до 80% времени на обработке запросов</p>
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="btn">Войти</a>
                <a href="{{ route('register') }}" class="btn btn-secondary">Регистрация</a>
            @else
                <a href="/dashboard" class="btn">Начать работу</a>
            @endif
        </div>
    </section>
    
    {{-- Footer --}}
    <footer>
        <p>&copy; {{ date('Y') }} AI Ответчик. Все права защищены.</p>
    </footer>
</body>
</html>
