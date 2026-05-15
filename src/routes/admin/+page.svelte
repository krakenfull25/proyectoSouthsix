<script>
	import { browser } from '$app/environment';
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';

	const API = 'https://pagamenos.net/api-forhim';

	let token = $state(null);
	let seccion = $state('clientes');

	// Datos
	let clientes  = $state([]);
	let productos = $state([]);
	let pedidos   = $state([]);
	let categorias = $state([]);
	let loading   = $state(true);
	let mensaje   = $state('');
	let error     = $state('');

	// Stock editable
	let stockEdit = $state({});

	// Formulario nuevo producto
	let nuevoProducto = $state({
		nombre: '',
		descripcion: '',
		precio: '',
		stock: '',
		id_categoria: ''
	});
	let loadingNuevo = $state(false);

	function authHeader() {
		return { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/x-www-form-urlencoded' };
	}

	function notify(msg, isError = false) {
		if (isError) error = msg;
		else mensaje = msg;
		setTimeout(() => { mensaje = ''; error = ''; }, 3000);
	}

	async function cargarClientes() {
		const res  = await fetch(`${API}/usuarios`, { headers: authHeader() });
		const data = await res.json();
		clientes   = (data.usuarios || []).filter(u => u.rol !== 'admin');
	}

	async function cargarProductos() {
		const res  = await fetch(`${API}/productos`);
		const data = await res.json();
		productos  = data.productos || [];
		productos.forEach(p => stockEdit[p.id] = p.stock ?? 0);
	}

	async function cargarCategorias() {
		const res  = await fetch(`${API}/categorias`);
		const data = await res.json();
		categorias = data.categorias || [];
	}

	async function cargarPedidos() {
		const res  = await fetch(`${API}/admin/pedidos`, { headers: authHeader() });
		const data = await res.json();
		pedidos = data.pedidos || [];
	}

	async function borrarCliente(id) {
		if (!confirm('¿Eliminar este cliente?')) return;
		const res  = await fetch(`${API}/admin/clientes/${id}`, { method: 'DELETE', headers: authHeader() });
		const data = await res.json();
		if (data.error) { notify(data.error, true); return; }
		notify(data.mensaje);
		clientes = clientes.filter(c => c.id !== id);
	}

	async function guardarStock(id) {
		const body = new URLSearchParams();
		body.append('stock', stockEdit[id]);
		const res  = await fetch(`${API}/admin/productos/${id}/stock`, { method: 'PUT', headers: authHeader(), body: body.toString() });
		const data = await res.json();
		if (data.error) { notify(data.error, true); return; }
		notify('Stock actualizado correctamente');
	}

	async function cambiarEstadoPedido(id, estado) {
		const body = new URLSearchParams();
		body.append('estado', estado);
		const res  = await fetch(`${API}/admin/pedidos/${id}`, { method: 'PUT', headers: authHeader(), body: body.toString() });
		const data = await res.json();
		if (data.error) { notify(data.error, true); return; }
		notify('Estado actualizado');
		pedidos = pedidos.map(p => p.id_pedido === id ? { ...p, estado } : p);
	}

	async function eliminarPedido(id) {
		if (!confirm('¿Eliminar este pedido y restaurar stock?')) return;
		const res  = await fetch(`${API}/admin/pedidos/${id}`, { method: 'DELETE', headers: authHeader() });
		const data = await res.json();
		if (data.error) { notify(data.error, true); return; }
		notify(data.mensaje);
		pedidos = pedidos.filter(p => p.id_pedido !== id);
	}

	async function añadirProducto(e) {
		e.preventDefault();
		if (!nuevoProducto.nombre || !nuevoProducto.precio || !nuevoProducto.stock || !nuevoProducto.id_categoria) {
			notify('Rellena todos los campos obligatorios', true);
			return;
		}

		loadingNuevo = true;
		const body = new URLSearchParams();
		body.append('nombre',       nuevoProducto.nombre);
		body.append('descripcion',  nuevoProducto.descripcion);
		body.append('precio',       nuevoProducto.precio);
		body.append('stock',        nuevoProducto.stock);
		body.append('id_categoria', nuevoProducto.id_categoria);

		const res  = await fetch(`${API}/admin/productos`, { method: 'POST', headers: authHeader(), body: body.toString() });
		const data = await res.json();
		loadingNuevo = false;

		if (data.error) { notify(data.error, true); return; }
		notify(`Producto añadido correctamente (ID: ${data.id_producto})`);

		// Limpiar formulario y recargar productos
		nuevoProducto = { nombre: '', descripcion: '', precio: '', stock: '', id_categoria: '' };
		await cargarProductos();
	}

	async function cambiarSeccion(s) {
		seccion = s;
		loading = true;
		if (s === 'clientes')  await cargarClientes();
		if (s === 'productos') { await cargarProductos(); await cargarCategorias(); }
		if (s === 'pedidos')   await cargarPedidos();
		loading = false;
	}

	function cerrarSesionAdmin() {
		localStorage.removeItem('token');
		localStorage.removeItem('usuario');
		goto('/');
	}

	onMount(async () => {
		token = localStorage.getItem('token');
		if (!token) { goto('/login'); return; }
		const usuario = JSON.parse(localStorage.getItem('usuario') || '{}');
		if (usuario.rol !== 'admin') { goto('/'); return; }

		history.pushState(null, '', window.location.href);
		window.addEventListener('popstate', () => cerrarSesionAdmin());

		await cargarClientes();
		loading = false;
	});

	const estados = ['pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado'];
</script>

<div class="admin">
	<aside class="sidebar">
		<div class="brand">
			<span class="brand-icon">⚙</span>
			<div>
				<p class="brand-title">FOR-HIM</p>
				<p class="brand-sub">Panel Admin</p>
			</div>
		</div>

		<nav>
			<button class:active={seccion === 'clientes'}  onclick={() => cambiarSeccion('clientes')}>
				<span>👤</span> Clientes
			</button>
			<button class:active={seccion === 'productos'} onclick={() => cambiarSeccion('productos')}>
				<span>📦</span> Productos
			</button>
			<button class:active={seccion === 'pedidos'}   onclick={() => cambiarSeccion('pedidos')}>
				<span>🧾</span> Pedidos
			</button>
		</nav>

		<button class="btn-salir" onclick={cerrarSesionAdmin}>← Cerrar sesión</button>
	</aside>

	<main class="panel">
		<div class="topbar">
			<h1>
				{#if seccion === 'clientes'}Gestión de Clientes
				{:else if seccion === 'productos'}Gestión de Productos
				{:else}Gestión de Pedidos{/if}
			</h1>
			{#if mensaje}<span class="toast ok">✓ {mensaje}</span>{/if}
			{#if error}<span class="toast err">✕ {error}</span>{/if}
		</div>

		{#if loading}
			<div class="loader">Cargando...</div>

		{:else if seccion === 'clientes'}
			<div class="tabla-wrap">
				<table>
					<thead>
						<tr>
							<th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Estado</th><th></th>
						</tr>
					</thead>
					<tbody>
						{#each clientes as c (c.id)}
							<tr>
								<td>#{c.id}</td>
								<td><strong>{c.username}</strong></td>
								<td>{c.nombre} {c.apellidos}</td>
								<td>{c.email}</td>
								<td>{c.telefono || '—'}</td>
								<td><span class="badge {c.estado}">{c.estado}</span></td>
								<td>
									<button class="btn-danger" onclick={() => borrarCliente(c.id)}>Eliminar</button>
								</td>
							</tr>
						{/each}
					</tbody>
				</table>
				{#if clientes.length === 0}<p class="empty">No hay clientes registrados</p>{/if}
			</div>

		{:else if seccion === 'productos'}

			<!-- FORMULARIO NUEVO PRODUCTO -->
			<div class="form-card">
				<h2 class="form-title">➕ Añadir nuevo producto</h2>
				<form onsubmit={añadirProducto} class="form-nuevo">
					<div class="form-grid">
						<div class="field">
							<label>Nombre *</label>
							<input type="text" placeholder="Ej: Champú Anticaída" bind:value={nuevoProducto.nombre} />
						</div>
						<div class="field">
							<label>Categoría *</label>
							<select bind:value={nuevoProducto.id_categoria}>
								<option value="">— Selecciona —</option>
								{#each categorias as cat}
									<option value={cat.id}>{cat.nombre}</option>
								{/each}
							</select>
						</div>
						<div class="field">
							<label>Precio (€) *</label>
							<input type="number" step="0.01" min="0" placeholder="9.99" bind:value={nuevoProducto.precio} />
						</div>
						<div class="field">
							<label>Stock *</label>
							<input type="number" min="0" placeholder="50" bind:value={nuevoProducto.stock} />
						</div>
						<div class="field col-2">
							<label>Descripción</label>
							<textarea placeholder="Descripción del producto..." bind:value={nuevoProducto.descripcion} rows="3"></textarea>
						</div>
					</div>
					<button type="submit" class="btn-primary" disabled={loadingNuevo}>
						{loadingNuevo ? 'Añadiendo...' : '➕ Añadir producto'}
					</button>
				</form>
			</div>

			<!-- TABLA PRODUCTOS -->
			<div class="tabla-wrap" style="margin-top: 24px;">
				<table>
					<thead>
						<tr>
							<th>ID</th><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th></th>
						</tr>
					</thead>
					<tbody>
						{#each productos as p (p.id)}
							<tr>
								<td>#{p.id}</td>
								<td><strong>{p.nombre_producto}</strong></td>
								<td>{p.tipo_producto}</td>
								<td>{p.precio.toFixed(2)} €</td>
								<td>
									<input type="number" min="0" bind:value={stockEdit[p.id]} class="stock-input" />
								</td>
								<td>
									<button class="btn-primary" onclick={() => guardarStock(p.id)}>Guardar</button>
								</td>
							</tr>
						{/each}
					</tbody>
				</table>
				{#if productos.length === 0}<p class="empty">No hay productos</p>{/if}
			</div>

		{:else if seccion === 'pedidos'}
			<div class="tabla-wrap">
				<table>
					<thead>
						<tr>
							<th>ID</th><th>Usuario</th><th>Email</th><th>Total</th><th>Dirección</th><th>Fecha</th><th>Estado</th><th></th>
						</tr>
					</thead>
					<tbody>
						{#each pedidos as p (p.id_pedido)}
							<tr>
								<td>#{p.id_pedido}</td>
								<td>{p.username}</td>
								<td>{p.email}</td>
								<td>{parseFloat(p.total).toFixed(2)} €</td>
								<td>{p.direccion_envio}</td>
								<td>{p.fecha_pedido?.slice(0, 10)}</td>
								<td>
									<select
										value={p.estado}
										onchange={(e) => cambiarEstadoPedido(p.id_pedido, e.target.value)}
									>
										{#each estados as est}
											<option value={est}>{est}</option>
										{/each}
									</select>
								</td>
								<td>
									<button class="btn-danger" onclick={() => eliminarPedido(p.id_pedido)}>Eliminar</button>
								</td>
							</tr>
						{/each}
					</tbody>
				</table>
				{#if pedidos.length === 0}<p class="empty">No hay pedidos registrados</p>{/if}
			</div>
		{/if}
	</main>
</div>

<style lang="scss">
	:global(body, html) {
		margin: 0;
		padding: 0;
		font-family: 'PT Sans Narrow', sans-serif;
		background: #f0f2f5;
	}
	:global(*) { box-sizing: border-box; }

	.admin {
		display: flex;
		min-height: 100vh;
	}

	.sidebar {
		width: 240px;
		min-height: 100vh;
		background: #2d3e40;
		display: flex;
		flex-direction: column;
		padding: 30px 20px;
		gap: 10px;
		position: sticky;
		top: 0;

		.brand {
			display: flex;
			align-items: center;
			gap: 12px;
			margin-bottom: 30px;

			.brand-icon { font-size: 28px; }
			.brand-title { margin: 0; color: white; font-size: 20px; font-weight: 700; }
			.brand-sub   { margin: 0; color: #9ab; font-size: 13px; }
		}

		nav {
			display: flex;
			flex-direction: column;
			gap: 8px;
			flex: 1;

			button {
				display: flex;
				align-items: center;
				gap: 10px;
				background: transparent;
				border: none;
				color: #cdd;
				font-size: 16px;
				font-family: 'PT Sans Narrow', sans-serif;
				padding: 12px 16px;
				border-radius: 8px;
				cursor: pointer;
				text-align: left;
				transition: background 0.2s, color 0.2s;

				&:hover { background: rgba(255,255,255,0.1); color: white; }
				&.active { background: #387373; color: white; font-weight: 700; }
			}
		}

		.btn-salir {
			background: transparent;
			border: 1px solid #c62828;
			color: #ef9a9a;
			font-size: 14px;
			font-family: 'PT Sans Narrow', sans-serif;
			padding: 10px;
			border-radius: 8px;
			cursor: pointer;
			transition: all 0.2s;

			&:hover { background: #c62828; color: white; }
		}
	}

	.panel {
		flex: 1;
		padding: 40px;
		overflow-x: auto;

		.topbar {
			display: flex;
			align-items: center;
			gap: 20px;
			margin-bottom: 30px;

			h1 { margin: 0; font-size: 28px; color: #2d3e40; }
		}

		.toast {
			padding: 8px 16px;
			border-radius: 6px;
			font-size: 14px;
			&.ok  { background: #e8f5e9; color: #2e7d32; }
			&.err { background: #fdecea; color: #c62828; }
		}

		.loader { text-align: center; padding: 60px; color: #888; font-size: 18px; }
		.empty  { text-align: center; color: #aaa; padding: 40px; }
	}

	.form-card {
		background: white;
		border-radius: 12px;
		padding: 28px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);

		.form-title {
			margin: 0 0 20px;
			font-size: 18px;
			color: #2d3e40;
		}
	}

	.form-nuevo {
		display: flex;
		flex-direction: column;
		gap: 16px;

		.form-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 14px;

			.col-2 { grid-column: span 2; }

			@media (max-width: 600px) {
				grid-template-columns: 1fr;
				.col-2 { grid-column: span 1; }
			}
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
			letter-spacing: 0.4px;
		}

		input, select, textarea {
			border: 1.5px solid #ddd;
			border-radius: 8px;
			padding: 8px 12px;
			font-size: 15px;
			font-family: 'PT Sans Narrow', sans-serif;
			outline: none;
			transition: border-color 0.2s;

			&:focus { border-color: #387373; box-shadow: 0 0 0 3px rgba(56,115,115,0.1); }
		}

		input, select { height: 42px; }
		textarea { resize: vertical; }
	}

	.tabla-wrap {
		background: white;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);

		table {
			width: 100%;
			border-collapse: collapse;

			thead tr {
				background: #2d3e40;
				color: white;
				th { padding: 14px 16px; text-align: left; font-size: 14px; font-weight: 600; }
			}

			tbody tr {
				border-bottom: 1px solid #f0f0f0;
				transition: background 0.15s;
				&:hover { background: #f9fafb; }
				&:last-child { border-bottom: none; }
				td { padding: 12px 16px; font-size: 15px; color: #333; }
			}
		}
	}

	.badge {
		padding: 4px 10px;
		border-radius: 20px;
		font-size: 13px;
		&.activo   { background: #e8f5e9; color: #2e7d32; }
		&.inactivo { background: #fdecea; color: #c62828; }
	}

	.stock-input {
		width: 80px;
		height: 32px;
		border: 1px solid #ddd;
		border-radius: 6px;
		padding: 0 8px;
		font-size: 15px;
		font-family: 'PT Sans Narrow', sans-serif;
	}

	select {
		height: 32px;
		border: 1px solid #ddd;
		border-radius: 6px;
		padding: 0 8px;
		font-size: 14px;
		font-family: 'PT Sans Narrow', sans-serif;
		cursor: pointer;
	}

	.btn-primary {
		background: #387373;
		color: white;
		border: none;
		border-radius: 6px;
		padding: 10px 20px;
		font-size: 15px;
		font-family: 'PT Sans Narrow', sans-serif;
		cursor: pointer;
		transition: background 0.2s;
		align-self: flex-start;

		&:hover:not(:disabled) { background: #2d5f5f; }
		&:disabled { background: #aaa; cursor: not-allowed; }
	}

	.btn-danger {
		background: #fdecea;
		color: #c62828;
		border: 1px solid #ef9a9a;
		border-radius: 6px;
		padding: 7px 14px;
		font-size: 14px;
		font-family: 'PT Sans Narrow', sans-serif;
		cursor: pointer;
		transition: background 0.2s;
		&:hover { background: #ffcdd2; }
	}

	@media (max-width: 768px) {
		.admin { flex-direction: column; }
		.sidebar {
			width: 100%;
			min-height: auto;
			flex-direction: row;
			flex-wrap: wrap;
			padding: 16px;
			nav { flex-direction: row; flex: none; }
		}
		.panel { padding: 20px; }
	}
</style>