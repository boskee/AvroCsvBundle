<?php
namespace Avro\CsvBundle\Form\Type;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * CSV Import Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ImportFormType extends AbstractType
{
    private $fileConstraints = array();

    public function __construct($removeConstrants = false)
    {
        if (!$removeConstrants) {
            $this->fileConstraints[] = new NotBlank();
        }
    }

    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delimiter', 'choice', array(
                'label' => 'import.form.delimiter',
                'constraints' => $this->fileConstraints,
                'choices' => array(
                    ',' => 'import.delimiter.comma',
                    ';' => 'import.delimiter.semicolon',
                    '|' => 'import.delimiter.pipe',
                    ':' => 'import.delimiter.colon'
                ),
                'translation_domain' => 'AvroCsvBundle'
            ))
            ->add('file', 'file', array(
                'label' => 'import.form.file',
                'constraints' => $this->fileConstraints,
                'required' => true,
                'translation_domain' => 'AvroCsvBundle'
            ))
            ->add('filename', 'hidden', array(
                'required' => false
            ))
            ->add('fields', 'collection', array(
                'label' => 'Fields',
                'required' => false,
                'type' => 'choice',
                'options' => array(
                    'choices' => $options['field_choices'],
                    'required' => false
                ),
                'allow_add' => true
            ));

        $builder->addEventListener(FormEvents::PRE_BIND, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data || !isset($data['file'])) {
                return;
            }

            $data['filename'] = $data['file']->getFilename();
            $event->setData($data);
        });
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver The resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'field_choices' => array()
        ));
    }

    /**
     * Get the forms name
     *
     * @return string name
     */
    public function getName()
    {
        return 'avro_csv_import';
    }
}
