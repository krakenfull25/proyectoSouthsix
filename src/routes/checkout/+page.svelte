<script>
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import Header from '$lib/Components/Header.svelte';
	import Footer from '$lib/Components/Footer.svelte';

	const API = 'https://pagamenos.net/api-forhim';

	let fields = $state({
		nombre: '',
		apellidos: '',
		calle: '',
		numero: '',
		ciudad: '',
		provincia: '',
		codigoPostal: '',
		pais: 'España',
		numeroTarjeta: '',
		nombreTitular: '',
		mes: '',
		ano: '',
		cvv: ''
	});

	let touched       = $state({});
	let loading       = $state(false);
	let submitError   = $state('');
	let submitSuccess = $state('');
	let cartProducts  = $state([]);
	let cart          = $state([]);
	let direcciones   = $state([]);
	let dirSeleccionada = $state('');

	onMount(async () => {
		// Cargar datos del usuario
		const usuario = browser ? JSON.parse(localStorage.getItem('usuario') || '{}') : {};
		if (usuario.nombre)    fields.nombre    = usuario.nombre;
		if (usuario.apellidos) fields.apellidos = usuario.apellidos;

		// Cargar carrito
		const stored = browser ? localStorage.getItem('cart') : null;
		cart = stored ? JSON.parse(stored) : [];

		if (cart.length) {
			const res  = await fetch(`${API}/productos`);
			const data = await res.json();
			const ids  = cart.map(c => c.id);
			cartProducts = (data.productos || []).filter(p => ids.includes(p.id));
		}

		// Cargar direcciones guardadas
		const token = browser ? localStorage.getItem('token') : null;
		if (token) {
			try {
				const res  = await fetch(`${API}/direcciones`, {
					headers: { 'Authorization': `Bearer ${token}` }
				});
				const data = await res.json();
				direcciones = data.direcciones || [];

				// Precargar dirección predeterminada si existe
				const predeterminada = direcciones.find(d => d.es_predeterminada == 1);
				if (predeterminada) aplicarDireccion(predeterminada);
			} catch {}
		}
	});

	function aplicarDireccion(dir) {
		fields.calle        = dir.calle        || '';
		fields.numero       = dir.numero       || '';
		fields.ciudad       = dir.ciudad       || '';
		fields.provincia    = dir.ciudad       || '';
		fields.codigoPostal = dir.codigo_postal|| '';
		fields.pais         = dir.pais         || 'España';
	}

	function onSelectDireccion() {
		if (!dirSeleccionada) return;
		const dir = direcciones.find(d => d.id_direccion == dirSeleccionada);
		if (dir) aplicarDireccion(dir);
	}

	let total = $derived(
		cartProducts.reduce((sum, p) => {
			const item = cart.find(c => c.id === p.id);
			return sum + p.precio * (item?.cantidad || 1);
		}, 0)
	);

	function touch(field) { touched[field] = true; }

	const rules = {
		nombre:        (v) => !v.trim() ? 'Obligatorio' : null,
		apellidos:     (v) => !v.trim() ? 'Obligatorio' : null,
		calle:         (v) => !v.trim() ? 'Obligatorio' : null,
		ciudad:        (v) => !v.trim() ? 'Obligatorio' : null,
		provincia:     (v) => !v.trim() ? 'Obligatorio' : null,
		codigoPostal:  (v) => !/^\d{5}$/.test(v) ? 'Debe tener 5 dígitos' : null,
		numeroTarjeta: (v) => !/^\d{16}$/.test(v) ? 'Debe tener 16 dígitos' : null,
		nombreTitular: (v) => !v.trim() ? 'Obligatorio' : null,
		mes:           (v) => !/^\d{2}$/.test(v) || +v < 1 || +v > 12 ? 'MM inválido' : null,
		ano:           (v) => !/^\d{4}$/.test(v) ? 'AAAA inválido' : null,
		cvv:           (v) => !/^\d{3,4}$/.test(v) ? 'CVV inválido' : null
	};

	let errors = $derived({
		nombre:        rules.nombre(fields.nombre),
		apellidos:     rules.apellidos(fields.apellidos),
		calle:         rules.calle(fields.calle),
		ciudad:        rules.ciudad(fields.ciudad),
		provincia:     rules.provincia(fields.provincia),
		codigoPostal:  rules.codigoPostal(fields.codigoPostal),
		numeroTarjeta: rules.numeroTarjeta(fields.numeroTarjeta),
		nombreTitular: rules.nombreTitular(fields.nombreTitular),
		mes:           rules.mes(fields.mes),
		ano:           rules.ano(fields.ano),
		cvv:           rules.cvv(fields.cvv)
	});

	let isValid = $derived(Object.values(errors).every(e => e === null));

	function soloNumeros(e, field, max) {
		fields[field] = e.target.value.replace(/\D/g, '').slice(0, max);
	}

	function formatTarjeta(e) {
		fields.numeroTarjeta = e.target.value.replace(/\D/g, '').slice(0, 16);
	}

	function tarjetaFormateada(v) {
		return v.replace(/(.{4})/g, '$1 ').trim();
	}

	async function comprar(event) {
		event.preventDefault();
		submitError = '';
		submitSuccess = '';
		Object.keys(fields).forEach(k => (touched[k] = true));
		if (!isValid) return;
		if (!cart.length) { submitError = 'El carrito está vacío.'; return; }

		const token = browser ? localStorage.getItem('token') : null;
		if (!token) { submitError = 'Debes iniciar sesión para completar la compra.'; return; }

		const productos = cart.map(item => ({ id_producto: item.id, cantidad: item.cantidad }));
		const direccion_envio = [fields.calle, fields.numero, fields.codigoPostal, fields.ciudad, fields.provincia, fields.pais].filter(Boolean).join(', ');

		const body = new URLSearchParams();
		body.append('productos', JSON.stringify(productos));
		body.append('direccion_envio', direccion_envio);

		loading = true;
		try {
			const res  = await fetch(`${API}/compra`, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Authorization': `Bearer ${token}` },
				body: body.toString()
			});
			const data = await res.json();
			if (!res.ok || data.error) { submitError = data.error || 'Error al realizar la compra.'; return; }
			submitSuccess = `¡Pedido #${data.id_pedido} confirmado! Total: ${data.total.toFixed(2)} €`;
			localStorage.removeItem('cart');
			setTimeout(() => window.location.href = '/', 2000);
		} catch {
			submitError = 'Error de conexión con el servidor.';
		} finally {
			loading = false;
		}
	}
</script>

<Header />

<div class="checkout">
	<div class="checkout-grid">

		<form onsubmit={comprar} class="form-card">

			<!-- DATOS PERSONALES -->
			<div class="section">
				<h2 class="section-title"><span class="step">1</span> Datos personales</h2>
				<div class="row-2">
					<div class="field">
						<label>Nombre</label>
						<input placeholder="Juan" bind:value={fields.nombre}
							onblur={() => touch('nombre')}
							class:err={touched.nombre && errors.nombre}
							class:ok={touched.nombre && !errors.nombre} />
						{#if touched.nombre && errors.nombre}<span class="error">{errors.nombre}</span>{/if}
					</div>
					<div class="field">
						<label>Apellidos</label>
						<input placeholder="García López" bind:value={fields.apellidos}
							onblur={() => touch('apellidos')}
							class:err={touched.apellidos && errors.apellidos}
							class:ok={touched.apellidos && !errors.apellidos} />
						{#if touched.apellidos && errors.apellidos}<span class="error">{errors.apellidos}</span>{/if}
					</div>
				</div>
			</div>

			<!-- DIRECCIÓN -->
			<div class="section">
				<h2 class="section-title"><span class="step">2</span> Dirección de envío</h2>

				{#if direcciones.length > 0}
					<div class="field">
						<label>Usar dirección guardada</label>
						<select bind:value={dirSeleccionada} onchange={onSelectDireccion}>
							<option value="">— Introducir manualmente —</option>
							{#each direcciones as dir}
								<option value={dir.id_direccion}>
									{dir.calle} {dir.numero}, {dir.ciudad} ({dir.tipo})
									{dir.es_predeterminada == 1 ? '⭐' : ''}
								</option>
							{/each}
						</select>
					</div>
					<div class="divider">o introduce una dirección nueva</div>
				{/if}

				<div class="row-3">
					<div class="field col-2">
						<label>Calle</label>
						<input placeholder="Calle Mayor" bind:value={fields.calle}
							onblur={() => touch('calle')}
							class:err={touched.calle && errors.calle}
							class:ok={touched.calle && !errors.calle} />
						{#if touched.calle && errors.calle}<span class="error">{errors.calle}</span>{/if}
					</div>
					<div class="field">
						<label>Número</label>
						<input placeholder="12" bind:value={fields.numero} />
					</div>
				</div>
				<div class="row-3">
					<div class="field">
						<label>Ciudad</label>
						<input placeholder="Madrid" bind:value={fields.ciudad}
							onblur={() => touch('ciudad')}
							class:err={touched.ciudad && errors.ciudad}
							class:ok={touched.ciudad && !errors.ciudad} />
						{#if touched.ciudad && errors.ciudad}<span class="error">{errors.ciudad}</span>{/if}
					</div>
					<div class="field">
						<label>Provincia</label>
						<input placeholder="Madrid" bind:value={fields.provincia}
							onblur={() => touch('provincia')}
							class:err={touched.provincia && errors.provincia}
							class:ok={touched.provincia && !errors.provincia} />
						{#if touched.provincia && errors.provincia}<span class="error">{errors.provincia}</span>{/if}
					</div>
					<div class="field">
						<label>Código Postal</label>
						<input placeholder="28001" bind:value={fields.codigoPostal}
							oninput={(e) => soloNumeros(e, 'codigoPostal', 5)}
							onblur={() => touch('codigoPostal')}
							class:err={touched.codigoPostal && errors.codigoPostal}
							class:ok={touched.codigoPostal && !errors.codigoPostal} />
						{#if touched.codigoPostal && errors.codigoPostal}<span class="error">{errors.codigoPostal}</span>{/if}
					</div>
				</div>
				<div class="field">
					<label>País</label>
					<select bind:value={fields.pais}>
						<option>España</option>
						<option>Portugal</option>
						<option>Francia</option>
						<option>Italia</option>
						<option>Alemania</option>
					</select>
				</div>
			</div>

			<!-- PAGO -->
			<div class="section">
				<h2 class="section-title"><span class="step">3</span> Datos de pago</h2>
				<div class="card-preview">
					<div class="card-number">{tarjetaFormateada(fields.numeroTarjeta) || '•••• •••• •••• ••••'}</div>
					<div class="card-bottom">
						<span>{fields.nombreTitular || 'NOMBRE TITULAR'}</span>
						<span>{fields.mes || 'MM'}/{fields.ano?.slice(-2) || 'AA'}</span>
					</div>
				</div>

				<div class="field">
					<label>Número de tarjeta</label>
					<input placeholder="1234 5678 9012 3456"
						value={tarjetaFormateada(fields.numeroTarjeta)}
						oninput={formatTarjeta}
						onblur={() => touch('numeroTarjeta')}
						class:err={touched.numeroTarjeta && errors.numeroTarjeta}
						class:ok={touched.numeroTarjeta && !errors.numeroTarjeta} />
					{#if touched.numeroTarjeta && errors.numeroTarjeta}<span class="error">{errors.numeroTarjeta}</span>{/if}
				</div>

				<div class="field">
					<label>Nombre del titular</label>
					<input placeholder="JUAN GARCIA" bind:value={fields.nombreTitular}
						onblur={() => touch('nombreTitular')}
						class:err={touched.nombreTitular && errors.nombreTitular}
						class:ok={touched.nombreTitular && !errors.nombreTitular} />
					{#if touched.nombreTitular && errors.nombreTitular}<span class="error">{errors.nombreTitular}</span>{/if}
				</div>

				<div class="row-3">
					<div class="field">
						<label>Mes</label>
						<input placeholder="MM" bind:value={fields.mes}
							oninput={(e) => soloNumeros(e, 'mes', 2)}
							onblur={() => touch('mes')}
							class:err={touched.mes && errors.mes}
							class:ok={touched.mes && !errors.mes} />
						{#if touched.mes && errors.mes}<span class="error">{errors.mes}</span>{/if}
					</div>
					<div class="field">
						<label>Año</label>
						<input placeholder="AAAA" bind:value={fields.ano}
							oninput={(e) => soloNumeros(e, 'ano', 4)}
							onblur={() => touch('ano')}
							class:err={touched.ano && errors.ano}
							class:ok={touched.ano && !errors.ano} />
						{#if touched.ano && errors.ano}<span class="error">{errors.ano}</span>{/if}
					</div>
					<div class="field">
						<label>CVV</label>
						<input placeholder="•••" bind:value={fields.cvv}
							oninput={(e) => soloNumeros(e, 'cvv', 4)}
							onblur={() => touch('cvv')}
							class:err={touched.cvv && errors.cvv}
							class:ok={touched.cvv && !errors.cvv} />
						{#if touched.cvv && errors.cvv}<span class="error">{errors.cvv}</span>{/if}
					</div>
				</div>
			</div>

			{#if submitError}<p class="msg-error">✕ {submitError}</p>{/if}
			{#if submitSuccess}<p class="msg-ok">✓ {submitSuccess}</p>{/if}

			<button type="submit" class="btn-comprar" disabled={!isValid || loading}>
				{loading ? 'Procesando...' : `Pagar ${total.toFixed(2)} €`}
			</button>
		</form>

		<!-- RESUMEN -->
		<div class="resumen">
			<div class="resumen-card">
				<h2>Resumen del pedido</h2>
				{#if cartProducts.length === 0}
					<p class="empty">El carrito está vacío</p>
				{:else}
					{#each cartProducts as p (p.id)}
						{@const item = cart.find(c => c.id === p.id)}
						<div class="resumen-item">
							<img src={p.imagenes?.[0]} alt={p.nombre_producto} />
							<div class="resumen-info">
								<p class="resumen-nombre">{p.nombre_producto}</p>
								<p class="resumen-qty">x{item?.cantidad || 1}</p>
							</div>
							<p class="resumen-precio">{(p.precio * (item?.cantidad || 1)).toFixed(2)} €</p>
						</div>
					{/each}
					<div class="resumen-total">
						<span>Total</span>
						<span>{total.toFixed(2)} €</span>
					</div>
				{/if}
			</div>
		</div>

	</div>
</div>

<Footer />

<style lang="scss">
	:global(body, html) {
		margin: 0;
		padding: 0;
		font-family: 'PT Sans Narrow', sans-serif;
		background: #f0f2f5;
	}
	:global(*) { box-sizing: border-box; }

	.checkout {
		max-width: 1100px;
		margin: 0 auto;
		padding: 40px 20px;
	}

	.checkout-grid {
		display: grid;
		grid-template-columns: 1fr 340px;
		gap: 30px;
		@media (max-width: 768px) { grid-template-columns: 1fr; }
	}

	.form-card {
		background: white;
		border-radius: 16px;
		padding: 36px;
		box-shadow: 0 2px 12px rgba(0,0,0,0.07);
		display: flex;
		flex-direction: column;
		gap: 32px;
	}

	.section {
		display: flex;
		flex-direction: column;
		gap: 14px;
	}

	.section-title {
		margin: 0;
		font-size: 18px;
		font-weight: 700;
		color: #2d3e40;
		display: flex;
		align-items: center;
		gap: 10px;

		.step {
			width: 28px;
			height: 28px;
			background: #2d3e40;
			color: white;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 14px;
			font-weight: 700;
			flex-shrink: 0;
		}
	}

	.divider {
		text-align: center;
		color: #aaa;
		font-size: 13px;
		position: relative;

		&::before, &::after {
			content: '';
			position: absolute;
			top: 50%;
			width: 40%;
			height: 1px;
			background: #eee;
		}
		&::before { left: 0; }
		&::after  { right: 0; }
	}

	.row-2 {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 12px;
		@media (max-width: 480px) { grid-template-columns: 1fr; }
	}

	.row-3 {
		display: grid;
		grid-template-columns: 1fr 1fr 1fr;
		gap: 12px;
		.col-2 { grid-column: span 2; }
		@media (max-width: 480px) {
			grid-template-columns: 1fr;
			.col-2 { grid-column: span 1; }
		}
	}

	.field {
		display: flex;
		flex-direction: column;
		gap: 5px;

		label {
			font-size: 13px;
			font-weight: 600;
			color: #555;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		input, select {
			height: 42px;
			border: 1.5px solid #ddd;
			border-radius: 8px;
			padding: 0 12px;
			font-size: 15px;
			font-family: 'PT Sans Narrow', sans-serif;
			transition: border-color 0.2s, box-shadow 0.2s;
			outline: none;

			&:focus {
				border-color: #387373;
				box-shadow: 0 0 0 3px rgba(56,115,115,0.12);
			}
			&.err {
				border-color: #e53935;
				box-shadow: 0 0 0 3px rgba(229,57,53,0.1);
			}
			&.ok { border-color: #43a047; }
		}

		.error { font-size: 12px; color: #e53935; }
	}

	.card-preview {
		background: linear-gradient(135deg, #2d3e40, #387373);
		border-radius: 14px;
		padding: 24px;
		color: white;

		.card-number {
			font-size: 20px;
			letter-spacing: 3px;
			margin-bottom: 20px;
			font-family: monospace;
		}

		.card-bottom {
			display: flex;
			justify-content: space-between;
			font-size: 14px;
			opacity: 0.85;
		}
	}

	.btn-comprar {
		height: 54px;
		background: #2d3e40;
		color: white;
		border: none;
		border-radius: 10px;
		font-size: 18px;
		font-family: 'PT Sans Narrow', sans-serif;
		font-weight: 700;
		cursor: pointer;
		transition: background 0.2s;
		margin-top: 8px;

		&:hover:not(:disabled) { background: #387373; }
		&:disabled { background: #aaa; cursor: not-allowed; }
	}

	.msg-error { color: #c62828; font-size: 14px; margin: 0; }
	.msg-ok    { color: #2e7d32; font-size: 14px; margin: 0; }

	.resumen-card {
		background: white;
		border-radius: 16px;
		padding: 28px;
		box-shadow: 0 2px 12px rgba(0,0,0,0.07);
		position: sticky;
		top: 20px;

		h2 { margin: 0 0 20px; font-size: 18px; color: #2d3e40; }
	}

	.resumen-item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 10px 0;
		border-bottom: 1px solid #f0f0f0;

		img {
			width: 55px;
			height: 55px;
			object-fit: cover;
			border-radius: 8px;
			background: #f5f5f5;
		}

		.resumen-info {
			flex: 1;
			p { margin: 0; }
			.resumen-nombre { font-size: 14px; font-weight: 600; color: #333; }
			.resumen-qty    { font-size: 13px; color: #999; }
		}

		.resumen-precio {
			margin: 0;
			font-weight: 700;
			font-size: 15px;
			color: #2d3e40;
		}
	}

	.resumen-total {
		display: flex;
		justify-content: space-between;
		margin-top: 16px;
		padding-top: 16px;
		border-top: 2px solid #2d3e40;
		font-size: 18px;
		font-weight: 700;
		color: #2d3e40;
	}

	.empty { color: #aaa; text-align: center; padding: 20px 0; }
</style>