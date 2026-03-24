<!--
Card de producto que se muestra en la página home.
-->

<div class="card">
	<div class="image">
		<p>Aqui va la imagen del producto que sea, más tarde se pasará por props.</p>
	</div>
	<div class="card-data">
		<p>Nombre de producto</p>
		<p>Tipo de producto</p>
		<div class="price">
			<p>Precio (€)</p>
			<p>8,99</p>
		</div>
		<div class="buttons">
			<p>&#x2764;</p>
			<p>&#128722;</p>
		</div>
	</div>
</div>

<style lang="scss">
	.card {
		border: 1px solid #e0e0e0;
		border-radius: 8px;
		overflow: hidden;
        
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
		transition:
			transform 0.3s ease,
			box-shadow 0.3s ease;

		&:hover {
			transform: translateY(-4px);
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
		}
	}

	.image {
		width: 100%;
		height: 200px;
		background-color: #f5f5f5;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #999;
	}

	.card-data {
		padding: 16px;
        
		p {
			margin: 0 0 8px 0;
			font-size: 14px;
			color: #333;

			&:first-child {
				font-weight: 600;
				font-size: 16px;
			}
		}
	}

	.price {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin: 12px 0;
		padding: 8px 0;
		border-top: 1px solid #f0f0f0;

		p:last-child {
			font-weight: 700;
			font-size: 18px;
			color: #007bff;
		}
	}

	.buttons {
		display: flex;
		gap: 8px;
		margin-top: 12px;
        justify-content: end;
        
        p{font-size: 30px;
          
        }
		
	}
</style>
