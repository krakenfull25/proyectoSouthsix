<script>
	import { browser } from '$app/environment';
	import User from '$lib/assets/icons/User.svg';

	const API = 'https://pagamenos.net/api-forhim';

	const usuario = browser ? JSON.parse(localStorage.getItem('usuario') || '{}') : {};
	const token   = browser ? localStorage.getItem('token') : null;

	let fields = $state({
		name:      usuario.username  || '',
		fullName:  usuario.nombre    || '',
		apellidos: usuario.apellidos || '',
		prefix:    'esp',
		phone:     usuario.telefono  || '',
		mail:      usuario.email     || ''
	});

	let touched = $state({
		name:      false,
		fullName:  false,
		apellidos: false,
		phone:     false,
		mail:      false
	});

	let guardado = $state(false);
	let loading  = $state(false);
	let errorMsg = $state('');

	const rules = {
		name: (v) => {
			if (!v.trim()) return 'El nombre de usuario es obligatorio.';
			if (v.trim().length < 3) return 'Mínimo 3 caracteres.';
			if (v.trim().length > 20) return 'Máximo 20 caracteres.';
			return null;
		},
		fullName: (v) => {
			if (!v.trim()) return 'El nombre completo es obligatorio.';
			if (v.trim().length < 2) return 'Mínimo 2 caracteres.';
			return null;
		},
		apellidos: (v) => {
			if (!v.trim()) return 'Los apellidos son obligatorios.';
			if (v.trim().length < 3) return 'Mínimo 3 caracteres.';
			return null;
		},
		phone: (v) => {
			if (!v.trim()) return 'El teléfono es obligatorio.';
			if (!/^\d{9}$/.test(v.trim())) return 'Debe contener exactamente 9 dígitos.';
			return null;
		},
		mail: (v) => {
			if (!v.trim()) return 'El correo electrónico es obligatorio.';
			if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())) return 'Formato de correo no válido.';
			return null;
		}
	};

	let errors = $derived({
		name:      rules.name(fields.name),
		fullName:  rules.fullName(fields.fullName),
		apellidos: rules.apellidos(fields.apellidos),
		phone:     rules.phone(fields.phone),
		mail:      rules.mail(fields.mail)
	});

	let isFormValid = $derived(Object.values(errors).every((e) => e === null));

	function touch(field) {
		touched[field] = true;
	}

	async function handleSubmit(e) {
		e.preventDefault();
		Object.keys(touched).forEach((k) => (touched[k] = true));
		if (!isFormValid) return;

		loading  = true;
		errorMsg = '';

		try {
			const body = new URLSearchParams();
			body.append('username',  fields.name);
			body.append('nombre',    fields.fullName);
			body.append('apellidos', fields.apellidos);
			body.append('telefono',  fields.phone);
			body.append('email',     fields.mail);

			const res = await fetch(`${API}/perfil`, {
				method: 'PUT',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
					'Authorization': `Bearer ${token}`
				},
				body: body.toString()
			});

			const data = await res.json();

			if (data.error) {
				errorMsg = data.error;
				return;
			}

			// Actualizar localStorage
			const usuarioActualizado = {
				...usuario,
				username:  fields.name,
				nombre:    fields.fullName,
				apellidos: fields.apellidos,
				telefono:  fields.phone,
				email:     fields.mail
			};
			localStorage.setItem('usuario', JSON.stringify(usuarioActualizado));

			guardado = true;
			setTimeout(() => (guardado = false), 3000);
		} catch (err) {
			errorMsg = 'Error de conexión con el servidor.';
		} finally {
			loading = false;
		}
	}
</script>

<div class="form-container">
	<div class="pfp-container">
		<img src={User} class="pfp" alt="Imagen de perfil" />
	</div>

	<div class="form">
		<form onsubmit={handleSubmit}>

			<div class="field-group">
				<label for="name">Nombre de usuario:</label>
				<input
					type="text"
					name="name"
					id="name"
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
				<label for="full-name">Nombre:</label>
				<input
					type="text"
					name="full-name"
					id="full-name"
					bind:value={fields.fullName}
					onblur={() => touch('fullName')}
					oninput={() => touch('fullName')}
					class:input-error={touched.fullName && errors.fullName}
					class:input-ok={touched.fullName && !errors.fullName}
				/>
				{#if touched.fullName && errors.fullName}
					<span class="error-msg">{errors.fullName}</span>
				{/if}
			</div>

			<div class="field-group">
				<label for="apellidos">Apellidos:</label>
				<input
					type="text"
					name="apellidos"
					id="apellidos"
					bind:value={fields.apellidos}
					onblur={() => touch('apellidos')}
					oninput={() => touch('apellidos')}
					class:input-error={touched.apellidos && errors.apellidos}
					class:input-ok={touched.apellidos && !errors.apellidos}
				/>
				{#if touched.apellidos && errors.apellidos}
					<span class="error-msg">{errors.apellidos}</span>
				{/if}
			</div>

			<div class="field-group">
				<label for="phone">Número de teléfono:</label>
				<div class="phone-container">
					<select name="prefix" id="prefix" bind:value={fields.prefix}>
						<option value="esp">+34</option>
						<option value="eng">+350</option>
					</select>
					<input
						type="text"
						name="phone"
						id="phone"
						bind:value={fields.phone}
						onblur={() => touch('phone')}
						oninput={() => touch('phone')}
						class:input-error={touched.phone && errors.phone}
						class:input-ok={touched.phone && !errors.phone}
					/>
				</div>
				{#if touched.phone && errors.phone}
					<span class="error-msg">{errors.phone}</span>
				{/if}
			</div>

			<div class="field-group">
				<label for="mail">Correo electrónico:</label>
				<input
					type="text"
					name="mail"
					id="mail"
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

			<button type="submit" class="btn-guardar" disabled={!isFormValid || loading}>
				{loading ? 'Guardando...' : 'Guardar cambios'}
			</button>

			{#if guardado}
				<span style="color: #43a047; font-size: 14px; text-align: center;">
					✓ Cambios guardados correctamente
				</span>
			{/if}

			{#if errorMsg}
				<span style="color: #e53935; font-size: 14px; text-align: center;">
					{errorMsg}
				</span>
			{/if}

		</form>
	</div>
</div>

<style lang="scss">



	:global(body, html) {
		margin: 0;
		padding: 0;
        font-family: 'PT Sans Narrow', sans-serif;
	}

	:global(*) {
		box-sizing: border-box;
	}

	.form-container {
		display: flex;
		flex-direction: column;
		margin: 0 26px;
		gap: 50px;

		> .pfp-container {
			margin: 0;
			display: flex;
			justify-content: center;

			> .pfp {
				width: 150px;
				height: auto;
				object-fit: contain;
				border: 2px solid black;
				padding: 10px;
			}
		}

		> .form > form {
			display: flex;
			flex-direction: column;
			gap: 40px;

			> .field-group {
				display: flex;
				flex-direction: column;

				> label {
					font-size: 24px;
					font-weight: 400;
					margin-bottom: 4px;
				}

				> input {
					height: 35px;
					border: 1px solid #ccc;
					border-radius: 4px;
					padding: 0 8px;
					transition: border-color 0.2s, box-shadow 0.2s;

					&.input-error {
						border-color: #e53935;
						box-shadow: 0 0 0 2px rgba(229, 57, 53, 0.2);
					}

					&.input-ok {
						border-color: #43a047;
						box-shadow: 0 0 0 2px rgba(67, 160, 71, 0.15);
					}
				}

				> .error-msg {
					margin-top: 4px;
					font-size: 13px;
					color: #e53935;
				}

				> .phone-container {
					display: flex;
					gap: 10px;

					> select,
					input {
						height: 35px;
					}

					> input {
						flex: 1;
						border: 1px solid #ccc;
						border-radius: 4px;
						padding: 0 8px;
						transition: border-color 0.2s, box-shadow 0.2s;

						&.input-error {
							border-color: #e53935;
							box-shadow: 0 0 0 2px rgba(229, 57, 53, 0.2);
						}

						&.input-ok {
							border-color: #43a047;
							box-shadow: 0 0 0 2px rgba(67, 160, 71, 0.15);
						}
					}
				}
			}

			> .btn-guardar {
				height: 65px;
				background-color: #e4f2e7;
				border: 1px solid black;
				border-radius: 10px;
				font-family: 'PT Sans Narrow', sans-serif;
				font-size: 24px;
				display: flex;
				align-items: center;
				justify-content: center;
				color: black;
				cursor: pointer;
				transition: opacity 0.2s, background-color 0.2s;

				&:disabled {
					opacity: 0.45;
					cursor: not-allowed;
					background-color: #d0d0d0;
				}

				&:not(:disabled):hover {
					background-color: #c8e6c9;
				}
			}
		}
	}

	@media (min-width: 1024px) {
		.form-container {
			display: flex;
			flex-direction: row;
			flex-wrap: wrap;
			margin: 0 90px;
			gap: 20px;
			justify-content: center;

			> .pfp-container {
				margin: 0;
				width: 120px;
				height: 180px;
				margin-right: 60px;
			}

			> .form {
				flex: 1 1 64%;
				min-width: 480px;
				max-width: 820px;
			}

			> .form > form {
				gap: 73px;
				width: 100%;
				margin-top: 10px;
			}
		}
	}
</style>