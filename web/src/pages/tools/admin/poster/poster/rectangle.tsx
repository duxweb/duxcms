import { useCallback, useContext, useEffect, useState } from 'react'
import { Button, Input, ColorPicker, Slider } from 'tdesign-react/esm'
import { PosterContext, SiderItem } from '../poster'
import { fabric } from 'fabric'

const PosterBtn = () => {
  const { editor } = useContext(PosterContext)

  const onAddRect = useCallback(() => {
    const rect = new fabric.Rect({
      top: 40,
      left: 40,
      width: 50,
      height: 50,
      fill: '#2151D1',
    })
    editor?.canvas.add(rect)
  }, [editor])

  const onAddCircle = useCallback(() => {
    const rect = new fabric.Circle({
      top: 40,
      left: 40,
      radius: 50,
      fill: '#2151D1',
    })
    editor?.canvas.add(rect)
  }, [editor])

  return (
    <>
      <Button
        theme='default'
        variant='text'
        onClick={onAddRect}
        icon={<div className='t-icon i-tabler:rectangle'></div>}
      >
        矩形
      </Button>
      <Button
        theme='default'
        variant='text'
        onClick={onAddCircle}
        icon={<div className='t-icon i-tabler:circle'></div>}
      >
        圆形
      </Button>
    </>
  )
}

const PosterTools = () => {
  const { editor, save } = useContext(PosterContext)
  const activeObject = editor?.canvas?.getActiveObject() as Record<string, any>
  const [label, setLabel] = useState<string>('')
  const [color, setColor] = useState<string>('')
  const [opacity, setOpacity] = useState<number>(1)

  useEffect(() => {
    setLabel(activeObject?.label)
    setColor(activeObject?.fill || 'rgb(0,0,0)')
    setOpacity(activeObject?.opacity || 1)
  }, [activeObject])

  if (activeObject?.get('type') !== 'rect' && activeObject?.get('type') !== 'circle') {
    return <></>
  }

  return (
    <>
      <SiderItem title='图片标签'>
        <Input
          value={label}
          onChange={(value) => {
            setLabel(value as string)
            activeObject.set('label', value)
            save()
          }}
        />
      </SiderItem>
      <SiderItem title='不透明度'>
        <Slider
          label
          layout='horizontal'
          value={opacity}
          max={1}
          min={0.1}
          step={0.1}
          onChange={(v) => {
            setOpacity(v as number)
            activeObject.set('opacity', v)
            save()
          }}
        />
      </SiderItem>
      <SiderItem title='背景色'>
        <ColorPicker
          value={color}
          format='HEX'
          onChange={(v) => {
            setColor(v)
            activeObject.set('fill', v)
            save()
          }}
        />
      </SiderItem>
    </>
  )
}

export const PosteRectangle = {
  Btn: PosterBtn,
  Tools: PosterTools,
}
