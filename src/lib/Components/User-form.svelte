

<div class="form-container">
	<div class="pfp-container">Imagen de perfil</div>
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
            <input type="submit" name="btnGuardar" value="Guardar cambios">
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
            margin: auto;
        }

        > .form > form {
            display: flex;
            flex-direction: column;
            gap: 40px;

            > .username, .full-name, .phone, .address, .mail {
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

                    > select, input {
                        height: 35px;
                    }

                    > input {
                        flex: 1;
                    }
                }
            }

            > input {
                height: 65px;
                background-color: #E4F2E7;
                border: 1px solid black;
                border-radius: 10px;
                font-family: 'PT Sans Narrow', sans-serif;
                font-size: 24px;
            }
        }
    }
</style>
