(function ($) {
	'use strict';

	// Sticky header shadow on scroll.
	var header = document.getElementById('site-header');
	function onScroll() {
		if (!header) return;
		if (window.scrollY > 12) header.classList.add('is-scrolled');
		else header.classList.remove('is-scrolled');
	}
	document.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	// Mobile menu toggle.
	var menuToggle = document.getElementById('menu-toggle');
	var mobilePanel = document.getElementById('mobile-nav-panel');
	if (menuToggle && mobilePanel) {
		menuToggle.addEventListener('click', function () {
			mobilePanel.hidden = !mobilePanel.hidden;
		});
	}

	// Search overlay.
	var searchOverlay = document.getElementById('search-overlay');
	function openSearch() {
		if (!searchOverlay) return;
		searchOverlay.hidden = false;
		var input = document.getElementById('search-overlay-input');
		if (input) setTimeout(function () { input.focus(); }, 50);
	}
	function closeSearch() {
		if (searchOverlay) searchOverlay.hidden = true;
	}
	['search-toggle', 'mobile-search-toggle'].forEach(function (id) {
		var el = document.getElementById(id);
		if (el) el.addEventListener('click', function (e) { e.preventDefault(); openSearch(); });
	});
	var searchClose = document.getElementById('search-close');
	if (searchClose) searchClose.addEventListener('click', closeSearch);

	// Instant search suggestions (debounced AJAX to WP REST search).
	var searchInput = document.getElementById('search-overlay-input');
	var suggestBox = document.getElementById('search-suggestions');
	var searchTimer;
	if (searchInput && suggestBox) {
		searchInput.addEventListener('input', function () {
			clearTimeout(searchTimer);
			var term = searchInput.value.trim();
			if (term.length < 2) {
				suggestBox.innerHTML = '';
				return;
			}
			searchTimer = setTimeout(function () {
				fetch('/wp-json/wp/v2/product?search=' + encodeURIComponent(term) + '&per_page=6')
					.then(function (r) { return r.ok ? r.json() : []; })
					.then(function (items) {
						if (!Array.isArray(items) || !items.length) {
							suggestBox.innerHTML = '<p class="no-results">No products found.</p>';
							return;
						}
						suggestBox.innerHTML = items.map(function (p) {
							return '<a class="suggestion-item" href="' + p.link + '">' + p.title.rendered + '</a>';
						}).join('');
					})
					.catch(function () {});
			}, 300);
		});
	}

	// Toast notifications.
	window.brewcartToast = function (message, type) {
		type = type || 'success';
		var container = document.getElementById('brewcart-toasts');
		if (!container) return;
		var toast = document.createElement('div');
		toast.className = 'bc-toast ' + type;
		toast.textContent = message;
		container.appendChild(toast);
		requestAnimationFrame(function () { toast.classList.add('show'); });
		setTimeout(function () {
			toast.classList.remove('show');
			setTimeout(function () { toast.remove(); }, 400);
		}, 3200);
	};

	// Wishlist toggle (event delegation).
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.wishlist-btn');
		if (!btn) return;
		e.preventDefault();
		var productId = btn.getAttribute('data-product-id');
		if (!productId || !window.BrewCart) return;

		fetch(window.BrewCart.ajaxUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'action=brewcart_wishlist_toggle&product_id=' + encodeURIComponent(productId) + '&nonce=' + encodeURIComponent(window.BrewCart.nonce)
		})
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (!res.success) {
					window.brewcartToast(res.data && res.data.message ? res.data.message : 'Something went wrong', 'error');
					return;
				}
				btn.classList.toggle('active', res.data.active);
				window.brewcartToast(res.data.message, 'success');
			})
			.catch(function () {
				window.brewcartToast('Network error, please try again', 'error');
			});
	});

	// WooCommerce add-to-cart success toast.
	$(document.body).on('added_to_cart', function () {
		window.brewcartToast('Added to cart', 'success');
	});
	$(document.body).on('wc_fragments_refreshed', function () {});

	// Scroll reveal.
	var revealEls = document.querySelectorAll('.reveal');
	if ('IntersectionObserver' in window && revealEls.length) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('in-view');
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.15 });
		revealEls.forEach(function (el) { io.observe(el); });
	} else {
		revealEls.forEach(function (el) { el.classList.add('in-view'); });
	}

	// Hero banner auto-scroller.
	(function () {
		var slider = document.getElementById('hero-slider');
		if (!slider) return;

		var slides = slider.querySelectorAll('.hero-slide');
		var dots = slider.querySelectorAll('.hero-dot');
		var prevBtn = document.getElementById('hero-prev');
		var nextBtn = document.getElementById('hero-next');
		var current = 0;
		var intervalMs = 5000;
		var timer = null;

		function goTo(index) {
			index = (index + slides.length) % slides.length;
			slides.forEach(function (s, i) {
				s.classList.toggle('is-active', i === index);
				s.setAttribute('aria-hidden', i === index ? 'false' : 'true');
			});
			dots.forEach(function (d, i) {
				d.classList.toggle('is-active', i === index);
				d.setAttribute('aria-selected', i === index ? 'true' : 'false');
			});
			current = index;
		}

		function next() { goTo(current + 1); }
		function prev() { goTo(current - 1); }

		function start() {
			stop();
			timer = setInterval(next, intervalMs);
		}
		function stop() {
			if (timer) clearInterval(timer);
			timer = null;
		}

		dots.forEach(function (dot, i) {
			dot.addEventListener('click', function () {
				goTo(i);
				start();
			});
		});
		if (nextBtn) nextBtn.addEventListener('click', function () { next(); start(); });
		if (prevBtn) prevBtn.addEventListener('click', function () { prev(); start(); });

		slider.addEventListener('mouseenter', stop);
		slider.addEventListener('mouseleave', start);
		slider.addEventListener('focusin', stop);
		slider.addEventListener('focusout', start);

		if (slides.length > 1) start();
	})();

	// Coffee Quiz.
	(function () {
		var quizSteps = document.querySelectorAll('.quiz-step');
		if (!quizSteps.length) return;
		var answers = {};
		var current = 0;
		var progressBar = document.getElementById('quiz-progress-bar');

		function showStep(i) {
			quizSteps.forEach(function (s, idx) { s.hidden = idx !== i; });
			if (progressBar) progressBar.style.width = (((i + 1) / quizSteps.length) * 100) + '%';
		}

		quizSteps.forEach(function (step) {
			var group = step.querySelector('.quiz-pills');
			if (!group) return;
			var field = group.getAttribute('data-field');
			group.querySelectorAll('.pill').forEach(function (pill) {
				pill.addEventListener('click', function () {
					group.querySelectorAll('.pill').forEach(function (p) { p.classList.remove('active'); });
					pill.classList.add('active');
					answers[field] = pill.getAttribute('data-value');

					if (current < quizSteps.length - 1) {
						current++;
						setTimeout(function () { showStep(current); }, 250);
					} else {
						setTimeout(brewcartSubmitQuiz, 250);
					}
				});
			});
		});

		function brewcartSubmitQuiz() {
			var stepsWrap = document.getElementById('quiz-steps');
			var results = document.getElementById('quiz-results');
			if (!window.BrewCart) return;

			var body = 'action=brewcart_quiz_match&nonce=' + encodeURIComponent(window.BrewCart.nonce);
			Object.keys(answers).forEach(function (k) { body += '&' + k + '=' + encodeURIComponent(answers[k]); });

			fetch(window.BrewCart.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body
			})
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (stepsWrap) stepsWrap.hidden = true;
					if (!results) return;
					results.hidden = false;
					if (!res.success || !res.data.products.length) {
						results.innerHTML = '<p>' + 'No matches found — browse our full shop instead.' + '</p>';
						return;
					}
					var html = '<h3>Your Perfect Matches</h3><div class="grid grid-3">';
					res.data.products.forEach(function (p) {
						html += '<a href="' + p.url + '" class="card" style="display:block;padding:16px;text-decoration:none;">' +
							(p.image ? '<img src="' + p.image + '" style="border-radius:8px;margin-bottom:10px;">' : '') +
							'<h4 style="margin:0 0 6px;color:var(--espresso);">' + p.name + '</h4>' +
							'<div>' + p.price + '</div></a>';
					});
					html += '</div>';
					results.innerHTML = html;
				})
				.catch(function () {
					window.brewcartToast('Something went wrong finding your match', 'error');
				});
		}

		showStep(0);
	})();

	// Customize Your Coffee — pill selectors + grind visibility + summary text.
	document.querySelectorAll('.option-pills').forEach(function (group) {
		var field = group.getAttribute('data-field');
		var hiddenInput = document.getElementById('brewcart_' + field);
		group.querySelectorAll('.pill').forEach(function (pill) {
			pill.addEventListener('click', function () {
				group.querySelectorAll('.pill').forEach(function (p) { p.classList.remove('active'); });
				pill.classList.add('active');
				if (hiddenInput) hiddenInput.value = pill.getAttribute('data-value');

				if (field === 'bean_ground') {
					var grindRow = document.querySelector('.grind-row');
					if (grindRow) grindRow.style.display = pill.getAttribute('data-value') === 'Ground' ? 'block' : 'none';
				}
				brewcartUpdateCustomizeSummary();
			});
		});
	});
	var grindSelect = document.querySelector('.customize-select');
	if (grindSelect) grindSelect.addEventListener('change', brewcartUpdateCustomizeSummary);

	function brewcartUpdateCustomizeSummary() {
		var summary = document.querySelector('.customize-summary .summary-text');
		if (!summary) return;
		var bean = document.getElementById('brewcart_bean_ground');
		var weight = document.getElementById('brewcart_weight');
		var parts = [];
		if (bean) parts.push(bean.value);
		var grindRow = document.querySelector('.grind-row');
		if (grindRow && grindRow.style.display !== 'none') {
			var grindSel = document.querySelector('.customize-select');
			if (grindSel) parts.push(grindSel.value);
		}
		if (weight) parts.push(weight.value);
		summary.textContent = parts.join(' · ');
	}

	// Contact form.
	(function () {
		var form = document.getElementById('brewcart-contact-form');
		if (!form) return;
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var statusEl = form.querySelector('.form-status');
			var data = new FormData(form);
			var body = 'action=brewcart_contact_submit&nonce=' + encodeURIComponent(window.BrewCart.nonce) +
				'&name=' + encodeURIComponent(data.get('name')) +
				'&email=' + encodeURIComponent(data.get('email')) +
				'&message=' + encodeURIComponent(data.get('message'));

			fetch(window.BrewCart.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body
			})
				.then(function (r) { return r.json(); })
				.then(function (res) {
					window.brewcartToast(res.data && res.data.message ? res.data.message : 'Something went wrong', res.success ? 'success' : 'error');
					if (statusEl) statusEl.textContent = res.data && res.data.message ? res.data.message : '';
					if (res.success) form.reset();
				})
				.catch(function () {
					window.brewcartToast('Network error, please try again', 'error');
				});
		});
	})();

	// Order tracking form.
	(function () {
		var form = document.getElementById('brewcart-track-form');
		if (!form) return;
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var results = document.getElementById('track-results');
			var data = new FormData(form);
			var body = 'action=brewcart_track_order&nonce=' + encodeURIComponent(window.BrewCart.nonce) +
				'&order_id=' + encodeURIComponent(data.get('order_id')) +
				'&contact=' + encodeURIComponent(data.get('contact'));

			fetch(window.BrewCart.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body
			})
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (!results) return;
					if (!res.success) {
						results.innerHTML = '<div class="card" style="padding:24px;color:var(--error);">' + (res.data && res.data.message ? res.data.message : 'Order not found') + '</div>';
						return;
					}
					var d = res.data;
					var stageLabels = { received: 'Received', processing: 'Processing', packed: 'Packed', shipped: 'Shipped', 'out-for-delivery': 'Out for Delivery', delivered: 'Delivered' };
					var html = '<div class="card reveal" style="max-width:640px;margin:0 auto;padding:32px;">';
					html += '<h3>Order #' + d.order_number + '</h3>';
					html += '<p style="color:var(--charcoal-soft);">Placed ' + d.date + ' &middot; Total: ' + d.total + '</p>';
					html += '<div class="track-timeline" style="display:flex;justify-content:space-between;margin:24px 0;flex-wrap:wrap;gap:8px;">';
					d.stages.forEach(function (stage, i) {
						var done = i <= d.current_idx;
						html += '<div style="flex:1;text-align:center;min-width:80px;">' +
							'<div style="width:14px;height:14px;border-radius:50%;margin:0 auto 6px;background:' + (done ? 'var(--amber)' : 'var(--latte)') + ';"></div>' +
							'<span style="font-size:.72rem;color:' + (done ? 'var(--espresso)' : 'var(--charcoal-soft)') + ';font-weight:' + (done ? '700' : '400') + ';">' + stageLabels[stage] + '</span></div>';
					});
					html += '</div><h4>Items</h4><ul>';
					d.items.forEach(function (item) { html += '<li>' + item.quantity + ' &times; ' + item.name + '</li>'; });
					html += '</ul></div>';
					results.innerHTML = html;
				})
				.catch(function () {
					window.brewcartToast('Network error, please try again', 'error');
				});
		});
	})();

	// Subscription form.
	(function () {
		var subForm = document.getElementById('brewcart-subscription-form');
		if (!subForm) return;

		var productSelect = document.getElementById('sub-product');
		var quantityInput = document.getElementById('sub-quantity');
		var freqGroup = document.getElementById('sub-frequency');
		var freqValue = document.getElementById('sub-frequency-value');
		var priceEl = document.getElementById('sub-price');
		var scheduleEl = document.getElementById('sub-schedule');
		var nextDateEl = document.getElementById('sub-next-date');

		var freqLabels = { weekly: 'Every week', biweekly: 'Every 2 weeks', monthly: 'Every month' };
		var freqDays = { weekly: 7, biweekly: 14, monthly: 30 };

		function updateSummary() {
			var opt = productSelect.options[productSelect.selectedIndex];
			var price = parseFloat(opt.getAttribute('data-price')) || 0;
			var qty = parseInt(quantityInput.value, 10) || 1;
			var discounted = (price * qty * 0.9).toFixed(2);
			if (priceEl) priceEl.textContent = '$' + discounted;
			if (scheduleEl) scheduleEl.textContent = freqLabels[freqValue.value];
			if (nextDateEl) {
				var d = new Date();
				d.setDate(d.getDate() + freqDays[freqValue.value]);
				nextDateEl.textContent = d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
			}
		}

		if (productSelect) productSelect.addEventListener('change', updateSummary);
		if (quantityInput) quantityInput.addEventListener('input', updateSummary);
		if (freqGroup) {
			freqGroup.querySelectorAll('.pill').forEach(function (pill) {
				pill.addEventListener('click', function () {
					freqGroup.querySelectorAll('.pill').forEach(function (p) { p.classList.remove('active'); });
					pill.classList.add('active');
					freqValue.value = pill.getAttribute('data-value');
					updateSummary();
				});
			});
		}
		updateSummary();

		subForm.addEventListener('submit', function (e) {
			e.preventDefault();
			var statusEl = subForm.querySelector('.form-status');
			var body = 'action=brewcart_add_subscription&nonce=' + encodeURIComponent(window.BrewCart.nonce) +
				'&product_id=' + encodeURIComponent(productSelect.value) +
				'&quantity=' + encodeURIComponent(quantityInput.value) +
				'&frequency=' + encodeURIComponent(freqValue.value);

			fetch(window.BrewCart.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body
			})
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (!res.success) {
						window.brewcartToast(res.data && res.data.message ? res.data.message : 'Something went wrong', 'error');
						return;
					}
					window.brewcartToast(res.data.message, 'success');
					if (statusEl) statusEl.textContent = res.data.message;
					setTimeout(function () { window.location.href = res.data.cart_url; }, 1200);
				})
				.catch(function () {
					window.brewcartToast('Network error, please try again', 'error');
				});
		});
	})();

	// Newsletter forms (AJAX, nonce-protected).
	var newsletterForms = document.querySelectorAll('#newsletter-form, #newsletter-form-inline');
	newsletterForms.forEach(function (newsletterForm) {
		newsletterForm.addEventListener('submit', function (e) {
			e.preventDefault();
			var email = newsletterForm.querySelector('[name="email"]').value;
			var nonce = newsletterForm.querySelector('[name="newsletter_nonce"]').value;
			fetch(window.BrewCart.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: 'action=brewcart_newsletter_signup&email=' + encodeURIComponent(email) + '&nonce=' + encodeURIComponent(nonce)
			})
				.then(function (r) { return r.json(); })
				.then(function (res) {
					window.brewcartToast(res.data && res.data.message ? res.data.message : 'Thanks for subscribing!', res.success ? 'success' : 'error');
					if (res.success) newsletterForm.reset();
				})
				.catch(function () {
					window.brewcartToast('Network error, please try again', 'error');
				});
		});
	});
})(jQuery);
