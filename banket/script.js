// ===== СЛАЙДЕР =====
let slides = document.querySelectorAll('.slide');
let current = 0;

function show(index) {
  slides.forEach(s => s.classList.remove('active'));
  current = (index + slides.length) % slides.length;
  slides[current].classList.add('active');
}

function move(direction) {
  show(current + direction);
}

if (slides.length) {
  setInterval(() => move(1), 3000);
}

// ===== МАСКА ТЕЛЕФОНА 8(XXX)XXX-XX-XX =====
document.querySelectorAll('.phone-mask').forEach(input => {
  input.addEventListener('input', event => {
    let digits = event.target.value.replace(/\D/g, '').slice(0, 11);
    if (digits.length && digits[0] !== '8') {
      digits = '8' + digits.slice(0, 10);
    }
    let result = '';
    if (digits.length >= 1) result = digits[0];
    if (digits.length >= 2) result += '(' + digits.slice(1, 4);
    if (digits.length >= 4) result += ')' + digits.slice(4, 7);
    if (digits.length >= 7) result += '-' + digits.slice(7, 9);
    if (digits.length >= 9) result += '-' + digits.slice(9, 11);
    event.target.value = result;
  });
});

// ===== МАСКА ДАТЫ ДД.ММ.ГГГГ =====
document.querySelectorAll('.date-mask').forEach(input => {
  input.addEventListener('input', event => {
    let digits = event.target.value.replace(/\D/g, '').slice(0, 8);
    let result = '';
    if (digits.length > 0) result = digits.slice(0, 2);
    if (digits.length >= 3) result += '.' + digits.slice(2, 4);
    if (digits.length >= 5) result += '.' + digits.slice(4, 8);
    event.target.value = result;
  });
});
