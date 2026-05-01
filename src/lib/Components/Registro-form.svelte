<script>
	import { goto } from '$app/navigation';

	let fields = $state({
		name: '',
		mail: '',
		password: '',
		confirmPassword: ''
	});

	let touched = $state({
		name: false,
		mail: false,
		password: false,
		confirmPassword: false
	});

	const rules = {
		name: (v) => {
			if (!v.trim()) return 'El nombre es obligatorio.';
			if (v.trim().length < 3) return 'Mínimo 3 caracteres.';
			if (v.trim().length > 20) return 'Máximo 20 caracteres.';
			return null;
		},
		mail: (v) => {
			if (!v.trim()) return 'El correo es obligatorio.';
			if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())) return 'Formato de correo no válido.';
			return null;
		},
		password: (v) => {
			if (!v) return 'La contraseña es obligatoria.';
			if (v.length < 6) return 'Mínimo 6 caracteres.';
			if (!/[A-Z]/.test(v)) return 'Debe contener al menos una mayúscula.';
			if (!/[0-9]/.test(v)) return 'Debe contener al menos un número.';
			return null;
		},
		confirmPassword: (v, password) => {
			if (!v) return 'Debes confirmar la contraseña.';
			if (v !== password) return 'Las contraseñas no coinciden.';
			return null;
		}
	};

	let errors = $derived({
		name: rules.name(fields.name),
		mail: rules.mail(fields.mail),
		password: rules.password(fields.password),
		confirmPassword: rules.confirmPassword(fields.confirmPassword, fields.password)
	});

	let isFormValid = $derived(Object.values(errors).every((e) => e === null));

	let passwordStrength = $derived((() => {
		const v = fields.password;
		if (!v) return 0;
		let score = 0;
		if (v.length >= 6) score++;
		if (/[A-Z]/.test(v) && /[0-9]/.test(v)) score++;
		if (v.length >= 10 && /[^A-Za-z0-9]/.test(v)) score++;
		return score;
	})());

	function touch(field) {
		touched[field] = true;
	}

	function handleSubmit(e) {
		e.preventDefault();
		Object.keys(touched).forEach((k) => (touched[k] = true));
		if (!isFormValid) return;

		// Guardar en localStorage
		const usuario = {
			name: fields.name,
			mail: fields.mail,
			password: fields.password
		};
		localStorage.setItem('usuario_registrado', JSON.stringify(usuario));

		goto('/login');
	}
</script>

<div class="auth-container">
	<div class="auth-card">
		<h1>Crear cuenta</h1>

		<form onsubmit={handleSubmit}>
			<div class="field-group">
				<label for="name">Nombre de usuario</label>
				<input
					type="text"
					id="name"
					name="name"
					placeholder="tunombre"
					bind:value={fields.name}
					onblur={() => touch('name')}
					oninput={() => touch('name')}
					class:input-error={touched.name && errors.name}
					class:input-ok={touched.name && !errors.name}
				/>
				{#if touched.name && errors.name}
					<span class="error-msg">{errors.name}</span>
				{/if}
			</div>

			<div class="field-group">
				<label for="mail">Correo electrónico</label>
				<input
					type="text"
					id="mail"
					name="mail"
					placeholder="ejemplo@correo.com"
					bind:value={fields.mail}
					onblur={() => touch('mail')}
					oninput={() => touch('mail')}
					class:input-error={touched.mail && errors.mail}
					class:input-ok={touched.mail && !errors.mail}
				/>
				{#if touched.mail && errors.mail}
					<span class="error-msg">{errors.mail}</span>
				{/if}
			</div>

			<div class="field-group">
				<label for="password">Contraseña</label>
				<input
					type="password"
					id="password"
					name="password"
					placeholder="••••••••"
					bind:value={fields.password}
					onblur={() => touch('password')}
					oninput={() => touch('password')}
					class:input-error={touched.password && errors.password}
					class:input-ok={touched.password && !errors.password}
				/>
				{#if touched.password && errors.password}
					<span class="error-msg">{errors.password}</span>
				{/if}
				{#if fields.password}
					<div class="strength-bar">
						<div
							class="strength-fill"
							class:weak={passwordStrength === 1}
							class:medium={passwordStrength === 2}
							class:strong={passwordStrength === 3}
							style="width: {(passwordStrength / 3) * 100}%"
						></div>
					</div>
					<span class="strength-label">
						{#if passwordStrength === 1}Débil{:else if passwordStrength === 2}Media{:else}Fuerte{/if}
					</span>
				{/if}
			</div>

			<div class="field-group">
				<label for="confirmPassword">Repetir contraseña</label>
				<input
					type="password"
					id="confirmPassword"
					name="confirmPassword"
					placeholder="••••••••"
					bind:value={fields.confirmPassword}
					onblur={() => touch('confirmPassword')}
					oninput={() => touch('confirmPassword')}
					class:input-error={touched.confirmPassword && errors.confirmPassword}
					class:input-ok={touched.confirmPassword && !errors.confirmPassword}
				/>
				{#if touched.confirmPassword && errors.confirmPassword}
					<span class="error-msg">{errors.confirmPassword}</span>
				{/if}
			</div>

			<button type="submit" class="btn-primary" disabled={!isFormValid}>
				Crear cuenta
			</button>

		</form>

		<p class="redirect">¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>
	</div>
</div>

<style lang="scss">
	.auth-container {
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 20px;
		background-color: #f5f5f5;
	}

	.auth-card {
		background: white;
		border: 1px solid #ddd;
		border-radius: 12px;
		padding: 40px 32px;
		width: 100%;
		max-width: 420px;

		h1 {
			font-size: 28px;
			font-weight: 600;
			margin-bottom: 32px;
			text-align: center;
		}

		form {
			display: flex;
			flex-direction: column;
			gap: 24px;
		}
	}

	.field-group {
		display: flex;
		flex-direction: column;
		gap: 6px;

		label {
			font-size: 15px;
			font-weight: 500;
		}

		input {
			height: 42px;
			border: 1px solid #ccc;
			border-radius: 6px;
			padding: 0 12px;
			font-size: 15px;
			transition: border-color 0.2s, box-shadow 0.2s;
			outline: none;

			&:focus {
				border-color: #555;
				box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.08);
			}

			&.input-error {
				border-color: #e53935;
				box-shadow: 0 0 0 2px rgba(229, 57, 53, 0.2);
			}

			&.input-ok {
				border-color: #43a047;
				box-shadow: 0 0 0 2px rgba(67, 160, 71, 0.15);
			}
		}

		.error-msg {
			font-size: 13px;
			color: #e53935;
		}
	}

	.strength-bar {
		height: 4px;
		background: #eee;
		border-radius: 2px;
		overflow: hidden;
		margin-top: 2px;

		.strength-fill {
			height: 100%;
			border-radius: 2px;
			transition: width 0.3s, background-color 0.3s;

			&.weak   { background-color: #e53935; }
			&.medium { background-color: #fb8c00; }
			&.strong { background-color: #43a047; }
		}
	}

	.strength-label {
		font-size: 12px;
		color: #888;
	}

	.btn-primary {
		height: 48px;
		background-color: #e4f2e7;
		border: 1px solid black;
		border-radius: 10px;
		font-family: 'PT Sans Narrow', sans-serif;
		font-size: 18px;
		cursor: pointer;
		transition: opacity 0.2s, background-color 0.2s;
		margin-top: 8px;

		&:disabled {
			opacity: 0.45;
			cursor: not-allowed;
			background-color: #d0d0d0;
		}

		&:not(:disabled):hover {
			background-color: #c8e6c9;
		}
	}

	.redirect {
		text-align: center;
		margin-top: 20px;
		font-size: 14px;

		a {
			color: #2e7d32;
			font-weight: 500;
			text-decoration: none;

			&:hover { text-decoration: underline; }
		}
	}
</style>