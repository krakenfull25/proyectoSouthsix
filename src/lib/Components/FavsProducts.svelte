<script>
    let { productos = [], loading = false } = $props();
   

    function removeFromFavorites(id) {
        const stored = JSON.parse(localStorage.getItem('favorites') ?? '[]');
        const updated = stored.filter(f => f !== id);
        localStorage.setItem('favorites', JSON.stringify(updated));

        productos = productos.filter(p => p.id !== id);

        const newIds = updated.join(',');
        history.replaceState({}, '', newIds ? `/lista-deseos?ids=${newIds}` : '/lista-deseos');
    }
</script>


<section class="wishlist-page">
    <div class="header">
        <h1>Lista de deseos</h1>
        <span class="count">{productos.length} productos</span>
    </div>

	{#if loading}
    <div class="empty-state">
        <p class="loading-text">Cargando...</p>
    </div>
    {:else if productos.length === 0}
        <div class="empty-state">
            <p class="empty-icon">♡</p>
            <p class="empty-text">Tu lista de deseos está vacía</p>
            <a href="/" class="btn-back">Explorar productos</a>
        </div>
    {:else}
        <div class="wishlist-grid">
            {#each productos as product (product.id)}
                <div class="product-card">
                    <div class="product-image">
                        {#if product.imagenes?.[0]}
                            <img src={product.imagenes[0]} alt={product.nombre_producto} />
                        {/if}
                    </div>
                    <div class="product-info">
                        <p class="product-name">{product.nombre_producto}</p>
                        <p class="product-type">{product.tipo_producto}</p>
                        <p class="product-price">{product.precio.toFixed(2)} €</p>
                    </div>
                    <div class="product-actions">
                        <a href="/product-detail/{product.id}" class="btn-view">Ver producto</a>
                        <button
                            class="btn-remove"
                            onclick={() => removeFromFavorites(product.id)}
                            aria-label="Eliminar de favoritos"
                        >✕</button>
                    </div>
                </div>
            {/each}
        </div>
    {/if}
</section>

<style lang="scss">

	section {
		min-height: 500px;
	}

	.wishlist-page {
		max-width: 860px;
		margin: 40px auto;
		padding: 0 20px;
		
	}

	.header {
		display: flex;
		align-items: baseline;
		gap: 14px;
		margin-bottom: 32px;

		h1 {
			color: #387373;
			font-size: 32px;
			margin: 0;
		}

		.count {
			color: #97a6a0;
			font-size: 14px;
		}
	}

	/* Estado vacío / loading */
	.empty-state {
		text-align: center;
		padding: 60px 20px;

		.empty-icon {
			font-size: 64px;
			color: #97a6a0;
			margin: 0 0 12px;
		}

		.loading-text,
		.empty-text {
			color: #97a6a0;
			font-size: 18px;
		}

		.btn-back {
			display: inline-block;
			margin-top: 20px;
			padding: 10px 24px;
			background: #387373;
			color: white;
			border-radius: 8px;
			text-decoration: none;
			font-size: 14px;
			transition: background 0.2s;

			&:hover {
				background: #2d5f5f;
			}
		}
	}

	/* Grid de productos */
	.wishlist-grid {
		display: flex;
		flex-direction: column;
		gap: 16px;
	}

	.product-card {
		display: flex;
		align-items: center;
		gap: 16px;
		background: white;
		border: 1px solid #e2e8e8;
		border-radius: 16px;
		padding: 14px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.07);
		transition: box-shadow 0.2s, transform 0.2s;

		&:hover {
			box-shadow: 0 4px 18px rgba(56, 115, 115, 0.15);
			transform: translateY(-2px);
		}

		.product-image {
			width: 90px;
			height: 90px;
			border-radius: 12px;
			background: #f0f4f4;
			overflow: hidden;
			flex-shrink: 0;

			img {
				width: 100%;
				height: 100%;
				object-fit: cover;
			}
		}

		.product-info {
			flex: 1;

			.product-name {
				margin: 0 0 4px;
				font-size: 16px;
				font-weight: 700;
				color: #1a2e2e;
			}

			.product-type {
				margin: 0 0 8px;
				font-size: 13px;
				color: #97a6a0;
			}

			.product-price {
				margin: 0;
				font-size: 18px;
				font-weight: 700;
				color: #387373;
			}
		}

		.product-actions {
			display: flex;
			flex-direction: column;
			align-items: flex-end;
			gap: 8px;

			.btn-view {
				padding: 8px 16px;
				background: #387373;
				color: white;
				border-radius: 8px;
				text-decoration: none;
				font-size: 13px;
				white-space: nowrap;
				transition: background 0.2s;

				&:hover {
					background: #2d5f5f;
				}
			}

			.btn-remove {
				background: none;
				border: none;
				color: #97a6a0;
				font-size: 16px;
				cursor: pointer;
				padding: 4px 8px;
				border-radius: 6px;
				transition: color 0.2s, background 0.2s;

				&:hover {
					color: #c0392b;
					background: #fef0ef;
				}
			}
		}
	}

	@media (max-width: 600px) {
		.product-card {
			flex-wrap: wrap;

			.product-actions {
				flex-direction: row;
				width: 100%;
				justify-content: space-between;
			}
		}
	}
</style>