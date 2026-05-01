<script>
	let fields = $state({
		nombre: '',
		apellidos: '',
		calle: '',
		numero: '',
		ciudad: '',
		provincia: '',
		codigoPostal: '',
		numeroTarjeta: '',
		nombreTitular: '',
		mes: '',
		ano: '',
		cvv: '',
		pais: ''
	});

	let touched = $state({});

	function touch(field) {
		touched[field] = true;
	}

	const rules = {
		nombre: (v) => (!v.trim() ? 'El nombre es obligatorio' : null),
		apellidos: (v) => (!v.trim() ? 'Los apellidos son obligatorios' : null),
		calle: (v) => (!v.trim() ? 'La calle es obligatoria' : null),
		ciudad: (v) => (!v.trim() ? 'La ciudad es obligatoria' : null),
		provincia: (v) => (!v.trim() ? 'La provincia es obligatoria' : null),

		codigoPostal: (v) =>
			!/^\d{5}$/.test(v) ? 'Código postal inválido (5 dígitos)' : null,

		numeroTarjeta: (v) =>
			!/^\d{16}$/.test(v) ? 'La tarjeta debe tener 16 dígitos' : null,

		nombreTitular: (v) => (!v.trim() ? 'El titular es obligatorio' : null),

		mes: (v) =>
			!/^\d{2}$/.test(v) || +v < 1 || +v > 12 ? 'Mes inválido' : null,

		ano: (v) =>
			!/^\d{4}$/.test(v) ? 'Año inválido' : null,

		cvv: (v) =>
			!/^\d{3,4}$/.test(v) ? 'CVV inválido' : null
	};

	let errors = $derived({
		nombre: rules.nombre(fields.nombre),
		apellidos: rules.apellidos(fields.apellidos),
		calle: rules.calle(fields.calle),
		ciudad: rules.ciudad(fields.ciudad),
		provincia: rules.provincia(fields.provincia),
		codigoPostal: rules.codigoPostal(fields.codigoPostal),
		numeroTarjeta: rules.numeroTarjeta(fields.numeroTarjeta),
		nombreTitular: rules.nombreTitular(fields.nombreTitular),
		mes: rules.mes(fields.mes),
		ano: rules.ano(fields.ano),
		cvv: rules.cvv(fields.cvv)
	});

	let isValid = $derived(Object.values(errors).every((e) => e === null));

	function soloNumeros(e, field, max) {
		const value = e.target.value.replace(/\D/g, '').slice(0, max);
		fields[field] = value;
	}

	function comprar() {
		Object.keys(fields).forEach((k) => (touched[k] = true));

		if (!isValid) return;

		alert('Compra realizada correctamente');

		localStorage.removeItem('cart');
		window.location.href = '/';
	}
</script>

<h1>Finalizar compra</h1>

<form onsubmit={comprar}>

	<h2>Datos personales</h2>

	<div class="field-group">
		<input placeholder="Nombre"
			bind:value={fields.nombre}
			onblur={() => touch('nombre')}
			class:input-error={touched.nombre && errors.nombre}
			class:input-ok={touched.nombre && !errors.nombre} />
		{#if touched.nombre && errors.nombre}<span class="error">{errors.nombre}</span>{/if}
	</div>

	<div class="field-group">
		<input placeholder="Apellidos"
			bind:value={fields.apellidos}
			onblur={() => touch('apellidos')}
			class:input-error={touched.apellidos && errors.apellidos}
			class:input-ok={touched.apellidos && !errors.apellidos} />
		{#if touched.apellidos && errors.apellidos}<span class="error">{errors.apellidos}</span>{/if}
	</div>

	<h2>Dirección</h2>

	<div class="field-group">
		<input placeholder="Calle"
			bind:value={fields.calle}
			onblur={() => touch('calle')}
			class:input-error={touched.calle && errors.calle} />
		{#if touched.calle && errors.calle}<span class="error">{errors.calle}</span>{/if}
	</div>

	<input placeholder="Número" bind:value={fields.numero} />

	<div class="field-group">
		<input placeholder="Ciudad"
			bind:value={fields.ciudad}
			onblur={() => touch('ciudad')}
			class:input-error={touched.ciudad && errors.ciudad} />
		{#if touched.ciudad && errors.ciudad}<span class="error">{errors.ciudad}</span>{/if}
	</div>

	<div class="field-group">
		<input placeholder="Provincia"
			bind:value={fields.provincia}
			onblur={() => touch('provincia')}
			class:input-error={touched.provincia && errors.provincia} />
		{#if touched.provincia && errors.provincia}<span class="error">{errors.provincia}</span>{/if}
	</div>

	<div class="field-group">
		<input placeholder="Código Postal"
			bind:value={fields.codigoPostal}
			oninput={(e) => soloNumeros(e, 'codigoPostal', 5)}
			onblur={() => touch('codigoPostal')}
			class:input-error={touched.codigoPostal && errors.codigoPostal} />
		{#if touched.codigoPostal && errors.codigoPostal}<span class="error">{errors.codigoPostal}</span>{/if}
	</div>

	<h2>Pago</h2>

	<div class="field-group">
		<input placeholder="Número tarjeta"
			bind:value={fields.numeroTarjeta}
			oninput={(e) => soloNumeros(e, 'numeroTarjeta', 16)}
			onblur={() => touch('numeroTarjeta')}
			class:input-error={touched.numeroTarjeta && errors.numeroTarjeta} />
		{#if touched.numeroTarjeta && errors.numeroTarjeta}<span class="error">{errors.numeroTarjeta}</span>{/if}
	</div>

	<div class="field-group">
		<input placeholder="Nombre titular"
			bind:value={fields.nombreTitular}
			onblur={() => touch('nombreTitular')}
			class:input-error={touched.nombreTitular && errors.nombreTitular} />
		{#if touched.nombreTitular && errors.nombreTitular}<span class="error">{errors.nombreTitular}</span>{/if}
	</div>

	<div class="row">
		<div class="field-group">
			<input placeholder="MM"
				bind:value={fields.mes}
				oninput={(e) => soloNumeros(e, 'mes', 2)}
				onblur={() => touch('mes')}
				class:input-error={touched.mes && errors.mes} />
			{#if touched.mes && errors.mes}<span class="error">{errors.mes}</span>{/if}
		</div>

		<div class="field-group">
			<input placeholder="AAAA"
				bind:value={fields.ano}
				oninput={(e) => soloNumeros(e, 'ano', 4)}
				onblur={() => touch('ano')}
				class:input-error={touched.ano && errors.ano} />
			{#if touched.ano && errors.ano}<span class="error">{errors.ano}</span>{/if}
		</div>
	</div>

	<div class="field-group">
		<input placeholder="CVV"
			bind:value={fields.cvv}
			oninput={(e) => soloNumeros(e, 'cvv', 4)}
			onblur={() => touch('cvv')}
			class:input-error={touched.cvv && errors.cvv} />
		{#if touched.cvv && errors.cvv}<span class="error">{errors.cvv}</span>{/if}
	</div>

	<button type="submit" disabled={!isValid}>
		Confirmar compra
	</button>
</form>

<style>
	h1 {
		text-align: center;
		color: #387373;
	}

	form {
		max-width: 500px;
		margin: 20px auto;
		display: flex;
		flex-direction: column;
		gap: 12px;
	}

	.field-group {
		display: flex;
		flex-direction: column;
		gap: 4px;
	}

	input {
		padding: 10px;
		border: 1px solid #ccc;
		border-radius: 8px;
	}

	.input-error {
		border: 2px solid red;
		background: #ffe6e6;
	}

	.input-ok {
		border: 2px solid green;
	}

	.error {
		color: red;
		font-size: 12px;
	}

	.row {
		display: flex;
		gap: 10px;
	}

	button {
		margin-top: 10px;
		padding: 12px;
		background: #387373;
		color: white;
		border: none;
		border-radius: 8px;
		cursor: pointer;
	}

	button:disabled {
		background: #aaa;
		cursor: not-allowed;
	}
</style>