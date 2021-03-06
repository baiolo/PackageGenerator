<?php

namespace WsdlToPhp\PackageGenerator\File;

use WsdlToPhp\PackageGenerator\Model\AbstractModel;
use WsdlToPhp\PackageGenerator\Model\Struct as StructModel;
use WsdlToPhp\PhpGenerator\Element\PhpAnnotation;
use WsdlToPhp\PhpGenerator\Element\PhpAnnotationBlock;
use WsdlToPhp\PhpGenerator\Element\PhpMethod;
use WsdlToPhp\PackageGenerator\Container\PhpElement\Method as MethodContainer;

class StructArray extends Struct
{
    /**
     * @var string
     */
    const METHOD_GET_ATTRIBUTE_NAME = 'getAttributeName';
    /**
     * @var string
     */
    const METHOD_CURRENT = 'current';
    /**
     * @var string
     */
    const METHOD_ITEM = 'item';
    /**
     * @var string
     */
    const METHOD_FIRST = 'first';
    /**
     * @var string
     */
    const METHOD_LAST = 'last';
    /**
     * @var string
     */
    const METHOD_OFFSET_GET = 'offsetGet';
    /**
     * @var string
     */
    const METHOD_ADD = 'add';
    /**
     * @see \WsdlToPhp\PackageGenerator\File\Struct::addStructMethodsSetAndGet()
     */
    public function addStructMethodsSetAndGet(MethodContainer $methods)
    {
        parent::addStructMethodsSetAndGet($methods);
        $this->addArrayMethodCurrent($methods)
            ->addArrayMethodItem($methods)
            ->addArrayMethodFirst($methods)
            ->addArrayMethodLast($methods)
            ->addArrayMethodOffsetGet($methods)
            ->addArrayMethodAdd($methods)
            ->addArrayMethodGetAttributeName($methods);
        return $this;
    }
    /**
     * @param MethodContainer $methods
     * @return StructArray
     */
    protected function addArrayMethodCurrent(MethodContainer $methods)
    {
        return $this->addArrayMethodGenericMethod($methods, self::METHOD_CURRENT, $this->getArrayMethodBody(self::METHOD_CURRENT));
    }
    /**
     * @param MethodContainer $methods
     * @return StructArray
     */
    protected function addArrayMethodItem(MethodContainer $methods)
    {
        return $this->addArrayMethodGenericMethod($methods, self::METHOD_ITEM, $this->getArrayMethodBody(self::METHOD_ITEM, '$index'), array(
            'index',
        ));
    }
    /**
     * @param MethodContainer $methods
     * @return StructArray
     */
    protected function addArrayMethodFirst(MethodContainer $methods)
    {
        return $this->addArrayMethodGenericMethod($methods, self::METHOD_FIRST, $this->getArrayMethodBody(self::METHOD_FIRST));
    }
    /**
     * @param MethodContainer $methods
     * @return StructArray
     */
    protected function addArrayMethodLast(MethodContainer $methods)
    {
        return $this->addArrayMethodGenericMethod($methods, self::METHOD_LAST, $this->getArrayMethodBody(self::METHOD_LAST));
    }
    /**
     * @param MethodContainer $methods
     * @return StructArray
     */
    protected function addArrayMethodOffsetGet(MethodContainer $methods)
    {
        return $this->addArrayMethodGenericMethod($methods, self::METHOD_OFFSET_GET, $this->getArrayMethodBody(self::METHOD_OFFSET_GET, '$offset'), array(
            'offset',
        ));
    }
    /**
     * @param MethodContainer $methods
     * @return StructArray
     */
    protected function addArrayMethodGetAttributeName(MethodContainer $methods)
    {
        return $this->addArrayMethodGenericMethod($methods, self::METHOD_GET_ATTRIBUTE_NAME, sprintf('return \'%s\';', $this->getModel()
            ->getAttributes()
            ->offsetGet(0)
            ->getName()));
    }
    /**
     * @param MethodContainer $methods
     * @return StructArray
     */
    protected function addArrayMethodAdd(MethodContainer $methods)
    {
        if ($this->getRestrictionFromStructAttribute() instanceof StructModel) {
            $method = new PhpMethod(self::METHOD_ADD, array(
                'item',
            ));
            $this->addStructMethodSetBodyForRestriction($method, $this->getModel()->getAttributes()->offsetGet(0), 'item');
            $method->addChild('return parent::add($item);');
            $methods->add($method);
        }
        return $this;
    }
    /**
     * @param MethodContainer $methods
     * @param string $name
     * @param string $body
     * @param string[] $methodParameters
     * @return StructArray
     */
    protected function addArrayMethodGenericMethod(MethodContainer $methods, $name, $body, array $methodParameters = array())
    {
        $method = new PhpMethod($name, $methodParameters);
        $method->addChild($body);
        $methods->add($method);
        return $this;
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getArrayMethodGetAttributeNameAnnotationBlock()
    {
        return new PhpAnnotationBlock(array(
            'Returns the attribute name',
            new PhpAnnotation(self::ANNOTATION_SEE, sprintf('%s::%s()', $this->getModel()->getExtends(true), self::METHOD_GET_ATTRIBUTE_NAME)),
            new PhpAnnotation(self::ANNOTATION_RETURN, sprintf('string %s', $this->getModel()->getAttributes()->offsetGet(0)->getName())),
        ));
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getArrayMethodCurrentAnnotationBlock()
    {
        return $this->getArrayMethodGenericAnnotationBlock(self::METHOD_CURRENT, 'Returns the current element');
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getArrayMethodFirstAnnotationBlock()
    {
        return $this->getArrayMethodGenericAnnotationBlock(self::METHOD_FIRST, 'Returns the first element');
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getArrayMethodLastAnnotationBlock()
    {
        return $this->getArrayMethodGenericAnnotationBlock(self::METHOD_LAST, 'Returns the last element');
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getArrayMethodItemAnnotationBlock()
    {
        return $this->getArrayMethodGenericAnnotationBlock(self::METHOD_ITEM, 'Returns the indexed element', 'int $index');
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getArrayMethodOffsetGetAnnotationBlock()
    {
        return $this->getArrayMethodGenericAnnotationBlock(self::METHOD_OFFSET_GET, 'Returns the element at the offset', 'int $offset');
    }
    /**
     * @return PhpAnnotationBlock
     */
    protected function getArrayMethodAddAnnotationBlock()
    {
        return new PhpAnnotationBlock(array(
            'Add element to array',
            new PhpAnnotation(self::ANNOTATION_SEE, sprintf('%s::add()', $this->getModel()->getExtends(true))),
            new PhpAnnotation(self::ANNOTATION_THROWS, '\InvalidArgumentException'),
            new PhpAnnotation(self::ANNOTATION_USES, sprintf('%s::valueIsValid()', $this->getModelFromStructAttribute()->getPackagedName(true))),
            new PhpAnnotation(self::ANNOTATION_PARAM, sprintf('%s $item', $this->getStructAttributeType())),
            new PhpAnnotation(self::ANNOTATION_RETURN, sprintf('%s', $this->getModel()->getPackagedName(true))),
        ));
    }
    /**
     * @param string $name
     * @param string $description
     * @param string $param
     * @return PhpAnnotationBlock
     */
    protected function getArrayMethodGenericAnnotationBlock($name, $description, $param = null)
    {
        $annotationBlock = new PhpAnnotationBlock(array(
            $description,
            new PhpAnnotation(self::ANNOTATION_SEE, sprintf('%s::%s()', $this->getModel()->getExtends(true), $name)),
        ));
        if (!empty($param)) {
            $annotationBlock->addChild(new PhpAnnotation(self::ANNOTATION_PARAM, $param));
        }
        $annotationBlock->addChild(new PhpAnnotation(self::ANNOTATION_RETURN, $this->getStructAttributeTypeGetAnnotation(null, false)));
        return $annotationBlock;
    }
    /**
     * @param PhpMethod $method
     * @return PhpAnnotationBlock|null
     */
    protected function getStructMethodAnnotationBlock(PhpMethod $method)
    {
        switch ($method->getName()) {
            case self::METHOD_GET_ATTRIBUTE_NAME:
                $annotationBlock = $this->getArrayMethodGetAttributeNameAnnotationBlock();
                break;
            case self::METHOD_CURRENT:
                $annotationBlock = $this->getArrayMethodCurrentAnnotationBlock();
                break;
            case self::METHOD_FIRST:
                $annotationBlock = $this->getArrayMethodFirstAnnotationBlock();
                break;
            case self::METHOD_ITEM:
                $annotationBlock = $this->getArrayMethodItemAnnotationBlock();
                break;
            case self::METHOD_LAST:
                $annotationBlock = $this->getArrayMethodLastAnnotationBlock();
                break;
            case self::METHOD_OFFSET_GET:
                $annotationBlock = $this->getArrayMethodOffsetGetAnnotationBlock();
                break;
            case self::METHOD_ADD:
                $annotationBlock = $this->getArrayMethodAddAnnotationBlock();
                break;
            default:
                $annotationBlock = parent::getStructMethodAnnotationBlock($method);
                break;
        }
        return $annotationBlock;
    }
    /**
     * @param string $method
     * @param string $var
     * @return string
     */
    protected function getArrayMethodBody($method, $var = '')
    {
        return sprintf('return parent::%s(%s);', $method, $var);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\File\AbstractModelFile::setModel()
     * @throws \InvalidArgumentException
     * @param AbstractModel $model
     * @return StructArray
     */
    public function setModel(AbstractModel $model)
    {
        if ($model instanceof StructModel && !$model->isArray()) {
            throw new \InvalidArgumentException('The model is not a valid array struct (name must contain Array and the model must contain only one property', __LINE__);
        }
        return parent::setModel($model);
    }
}
