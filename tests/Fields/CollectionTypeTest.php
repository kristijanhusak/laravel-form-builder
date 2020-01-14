<?php

namespace {

    use Kris\LaravelFormBuilder\Fields\ChoiceType;
    use Kris\LaravelFormBuilder\Fields\CollectionType;
    use Kris\LaravelFormBuilder\Fields\SelectType;
    use Kris\LaravelFormBuilder\Form;
    use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
    use Illuminate\Database\Eloquent\Model;

    class CollectionTypeTest extends FormBuilderTestCase
    {
        use InteractsWithSession;

        /** @test */
        public function it_creates_collection()
        {
            $options = [
                'type' => 'select',
                'data' => [['id' => 1], ['id' => 2], ['id' => 3]],
                'options' => [
                    'choices' => ['m' => 'male', 'f' => 'female'],
                    'label' => false
                ]
            ];

            $emailsCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

            $this->assertEquals(3, count($emailsCollection->getChildren()));
            $this->assertInstanceOf('Kris\LaravelFormBuilder\Fields\SelectType', $emailsCollection->prototype());
        }

        /** @test */
        public function it_creates_collection_with_empty_row()
        {
            $options = [
                'type' => 'text'
            ];

            $emailsCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

            $this->assertEquals(1, count($emailsCollection->getChildren()));
        }

        /** @test */
        public function it_creates_collection_without_empty_row()
        {
            $options = [
                'type' => 'text',
                'empty_row' => false
            ];

            $emailsCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

            $this->assertEquals(0, count($emailsCollection->getChildren()));
        }

        /** @test */
        public function it_uses_old_input_if_available()
        {
            $options = [
                'type' => 'select',
                'data' => [['id' => 4], ['id' => 5], ['id' => 6]],
                'options' => [
                    'choices' => ['m' => 'male', 'f' => 'female'],
                    'label' => false
                ]
            ];

            $this->session([
                '_old_input' => ['emails' => [['id' => 1], ['id' => 2]]]
            ]);

            $emailsCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

            $this->assertEquals(2, count($emailsCollection->getChildren()));
            $this->assertInstanceOf('Kris\LaravelFormBuilder\Fields\SelectType', $emailsCollection->prototype());
        }

        /** @test */
        public function it_creates_collection_with_child_form()
        {
            $form = clone $this->plainForm;

            $form->add('name', 'text')
                ->add('gender', 'choice', [
                    'choices' => ['m' => 'male', 'f' => 'female']
                ])
                 ->add('published', 'checkbox');

            $data = new \Illuminate\Support\Collection([
                ['name' => 'john doe', 'gender' => 'm'],
                ['name' => 'jane doe', 'gender' => 'f']
            ]);

            $options = [
                'type' => 'form',
                'data' => $data,
                'options' => [
                    'class' => $form
                ]
            ];

            $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

            $childFormCollection->render();

            $this->assertEquals(2, count($childFormCollection->getChildren()));
        }

        /** @test */
        public function it_creates_collection_with_child_form_with_correct_model()
        {
            $model = new DummyEloquentModel();
            $form = clone $this->plainForm;
            $form->setModel($model);

            $data = new \Illuminate\Support\Collection([
                new DummyEloquentModel2(),
                new DummyEloquentModel2(),
            ]);

            $options = [
                'type' => 'form',
                'data' => $data,
                'options' => [
                    'class' => '\LaravelFormBuilderCollectionTypeTest\Forms\NamespacedDummyForm'
                ]
            ];

            $childFormCollection = new CollectionType('models', 'collection', $form, $options);

            $childFormCollection->render();

            foreach ($childFormCollection->getChildren() as $child)
            {
                $this->assertInstanceOf(DummyEloquentModel2::class, $child->getModel());
            }
        }

        /**
         * @test
         */
        public function it_throws_exception_when_requesting_prototype_while_it_is_disabled()
        {
            $this->expectException(\Exception::class);

            $options = [
                'type' => 'text',
                'prototype' => false
            ];

            $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);

            $childFormCollection->render();

            $childFormCollection->prototype();
        }

        /**
         * @test
         */
        public function it_throws_exception_when_creating_nonexisting_type()
        {
            $this->expectException(\Exception::class);

            $options = [
                'type' => 'nonexisting'
            ];

            $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);
        }

        /**
         * @test
         */
        public function it_throws_exception_when_data_is_not_iterable()
        {
            $this->expectException(\Exception::class);

            $options = [
                'type' => 'text',
                'data' => 'invalid'
            ];

            $childFormCollection = new CollectionType('emails', 'collection', $this->plainForm, $options);
        }

        /**
         * @test
         */
        public function it_sets_up_prototype_with_empty_values()
        {
            $form = $this->formBuilder->plain([
                'model' => [
                    'title' => 'main form title'
                ]
            ])->add('title', 'text');

                $form->add('dummy_collection', 'collection', [
                'type' => 'form',
                'options' => [
                    'class' => CustomDummyForm::class
                ]
            ]);

            $this->assertNull(
                $form->dummy_collection->prototype()->title->getValue()
            );
        }
    }

    class DummyEloquentModel extends Model {

    }

    class DummyEloquentModel2 extends Model {

    }
}

namespace LaravelFormBuilderCollectionTypeTest\Forms {

    use Kris\LaravelFormBuilder\Form;

    class NamespacedDummyForm extends Form
    {
    }
}
