# TABBIFY

	<div class="column c33">

		 <DIV class="tabify">
			<a class="tab" href="#Primary"><i class="fa fa-cog"></i> Primary</a>
			<a class="tab" href="#Secondary"><i class="fa fa-institution"></i> Secondary</a>
		</DIV>

	</div>

	<div class="column c66">

		<div class="tabified">

			<div id="Primary" class="tab">
				Primary content
			</div>

			<div id="Secondary" class="tab">
				Secondary Content
			</div>

		</div>

	</div>


# FORM STRUCT HELPERS
## SIMPLE INPUT
	<div class="form-group">
		<div class="input">
			<label for="MODEL_FIELD" class="control-label">LABEL</label>
			<?php echo $this -> Form -> input("FIELD", array('pure' => true)); ?>
		</div>
	</div>

## LABEL CLASS FOR ROWS
	<div class="label"><label for="agencias_telefono_alternativo_lada" class="control-row-label">TELÉFONO</label></div>
## ROW

	<div class="form-group row lined">
		<div class="detail">
			<label for="agencias_telefono_alternativo_lada" class="control-row-label">LABEL GENERAL</label>
		</div>
		<div class="input column c25">
			<label for="" class="control-label complementary">LABEL1</label>
			<?php echo $this -> Form -> input("FIELD1", array('pure' => true)); ?>
		</div>
		<div class="input column c75">
			<label for="" class="control-label complementary">LABEL2</label>
			<?php echo $this -> Form -> input("FIELD2", array('pure' => true)); ?>
		</div>
	</div>