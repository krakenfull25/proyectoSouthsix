<script>
	import User from '$lib/assets/icons/User.svg';
</script>

<div class="form-container">
	<div class="pfp-container">
		<img src={User} class="pfp" alt="Imagen de perfil" />
	</div>

	<div class="form">
		<form action="." method="post">
			<div class="username">
				<label for="name">Nombre de usuario:</label>
				<input type="text" name="name" id="name" />
			</div>
			<div class="full-name">
				<label for="full-name">Dirección:</label>
				<input type="text" name="full-name" id="full-name" />
			</div>
			<div class="phone">
				<label for="phone">Número de teléfono:</label>
				<div class="phone-container">
					<select name="prefix" id="prefix">
						<option value="esp">+34</option>
						<option value="eng">+350</option>
					</select>
					<input type="text" name="phone" id="phone" />
				</div>
			</div>
			<div class="address">
				<label for="address">Dirección:</label>
				<input type="text" name="address" id="address" />
			</div>
			<div class="mail">
				<label for="mail">Correo electrónico:</label>
				<input type="text" name="mail" id="mail" />
			</div>
			<a href=".." name="btnGuardar">Guardar cambios</a>
		</form>
	</div>
</div>

<style lang="scss">
	.form-container {
		display: flex;
		flex-direction: column;
		margin: 0 26px;
		gap: 50px;
        

		> .pfp-container {
            margin:0;
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

			> .username,
			.full-name,
			.phone,
			.address,
			.mail {
				display: flex;
				flex-direction: column;

				> label {
					font-size: 24px;
					font-weight: 400;
				}

				> input {
					height: 35px;
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
					}
				}
			}

			> a {
				height: 65px;
				background-color: #e4f2e7;
				border: 1px solid black;
				border-radius: 10px;
				font-family: 'PT Sans Narrow', sans-serif;
				font-size: 24px;
				display: flex;
				align-items: center;
				justify-content: center;
				text-decoration: none;
				color: black;
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
