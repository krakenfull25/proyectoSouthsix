<script>
	import { goto } from '$app/navigation';

	let fields = $state({ mail: '', password: '' });
	let touched = $state({ mail: false, password: false });

	const rules = {
		mail: (v) => {
			if (!v.trim()) return 'El correo es obligatorio.';
			if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())) return 'Formato de correo no válido.';
			return null;
		},
		password: (v) => {
			if (!v.trim()) return 'La contraseña es obligatoria.';
			if (v.length < 6) return 'Mínimo 6 caracteres.';
			return null;
		}
	};

	let errors = $derived({
		mail: rules.mail(fields.mail),
		password: rules.password(fields.password)
	});

	let isFormValid = $derived(Object.values(errors).every((e) => e === null));

	function touch(field) {
		touched[field] = true;
	}

	let errorLogin = $state('');

	function handleSubmit(e) {
		e.preventDefault();
		Object.keys(touched).forEach((k) => (touched[k] = true));
		if (!isFormValid) return;

		const usuarioGuardado = localStorage.getItem('usuario_registrado');
		if (!usuarioGuardado) {
			errorLogin = 'No existe ningún usuario registrado.';
			return;
		}

		const usuario = JSON.parse(usuarioGuardado);
		if (usuario.mail !== fields.mail || usuario.password !== fields.password) {
			errorLogin = 'Correo o contraseña incorrectos.';
			return;
		}

		// Guardar sesión activa
		localStorage.setItem('sesion_activa', JSON.stringify({ name: usuario.name, mail: usuario.mail }));

		goto('/');
	}
</script>

<div class="auth-container">
	<div class="auth-card">
		<h1>Iniciar sesión</h1>

		<form onsubmit={handleSubmit}>
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
			</div>

			<button type="submit" class="btn-primary" disabled={!isFormValid}>
				Entrar
			</button>

			{#if errorLogin}
				<span class="error-msg" style="text-align:center">{errorLogin}</span>
			{/if}

		</form>

		<p class="redirect">¿No tienes cuenta? <a href="/registro">Regístrate aquí</a></p>
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

			&:hover {
				text-decoration: underline;
			}
		}
	}
</style>