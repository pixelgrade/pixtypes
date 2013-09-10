<?php

interface PixTypesFormRendererInterface
{
    public function render(PixTypesFormField $form);

    public function setManager(PixTypesFormManagerInterface $manager);
}