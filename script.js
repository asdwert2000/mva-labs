// ===== ОТПРАВКА ФОРМЫ В TELEGRAM (AJAX) =====
const form = document.getElementById('telegramForm');
const statusDiv = document.getElementById('formStatus');

if (form) {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.textContent;
        
        // Показываем загрузку
        submitBtn.textContent = '⏳ Отправка...';
        submitBtn.disabled = true;
        statusDiv.textContent = '';
        statusDiv.style.color = '#9CA3AF';
        
        try {
            const formData = new FormData(form);
            const response = await fetch('bot.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                statusDiv.textContent = '✅ Заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.';
                statusDiv.style.color = '#4ADE80';
                form.reset();
            } else {
                statusDiv.textContent = '❌ Ошибка при отправке. Попробуйте ещё раз или напишите в Telegram.';
                statusDiv.style.color = '#F87171';
            }
        } catch (error) {
            statusDiv.textContent = '❌ Ошибка соединения. Попробуйте ещё раз.';
            statusDiv.style.color = '#F87171';
            console.error('Ошибка:', error);
        } finally {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
}