import { useCallback, useContext, useState } from 'react'
import { Button, ColorPicker, Radio } from 'tdesign-react/esm'
import { PosterContext, SiderItem } from '../poster'
import { fabric } from 'fabric'

const PosterBtn = () => {
  const { editor } = useContext(PosterContext)

  const onAddText = useCallback(() => {
    const text = new fabric.Textbox('请输入文本', {
      fontSize: 24,
      lockUniScaling: true,
    })
    editor?.canvas.add(text)
  }, [editor])

  return (
    <Button
      theme='default'
      variant='text'
      onClick={onAddText}
      icon={<div className='t-icon i-tabler:letter-t'></div>}
    >
      文本
    </Button>
  )
}

const PosterTools = () => {
  const { editor, save } = useContext(PosterContext)
  const activeObject = editor?.canvas?.getActiveObject() as Record<string, any>
  const [textColor, setTextColor] = useState<string>(activeObject?.fill || 'rgb(0,0,0)')
  const [textAlign, setTextAlign] = useState<string>(activeObject?.textAlign || 'left')
  const [fontWeight, setFontWeight] = useState<string>(activeObject?.fontWeight || 'normal')
  const [fontStyle, setFontStyle] = useState<string>(activeObject?.fontStyle || 'normal')
  const [underline, setUnderline] = useState<boolean>(!!activeObject?.underline)

  if (activeObject?.get('type') !== 'textbox') {
    return <></>
  }

  return (
    <>
      <SiderItem title='文本颜色'>
        <ColorPicker
          value={textColor}
          onChange={(v) => {
            setTextColor(v)
            activeObject.set('fill', v)
            save()
          }}
        />
      </SiderItem>

      <SiderItem title='文本对齐'>
        <Radio.Group
          className='w-full'
          value={textAlign}
          onChange={(value) => {
            setTextAlign(value as string)
            activeObject.set('textAlign', value)
            save()
          }}
        >
          <Radio.Button value='left' className='flex-1 justify-center'>
            <div className='t-icon i-tabler:align-left'></div>
          </Radio.Button>
          <Radio.Button value='center' className='flex-1 justify-center'>
            <div className='t-icon i-tabler:align-center'></div>
          </Radio.Button>
          <Radio.Button value='right' className='flex-1 justify-center'>
            <div className='t-icon i-tabler:align-right'></div>
          </Radio.Button>
        </Radio.Group>
      </SiderItem>
      <SiderItem title='文本格式'>
        <div className='grid grid-cols-3 gap-2'>
          <Button
            variant='outline'
            theme={fontWeight == 'bold' ? 'primary' : 'default'}
            icon={<div className='t-icon i-tabler:bold'></div>}
            onClick={() => {
              console.log(fontWeight)
              const value = fontWeight == 'bold' ? 'normal' : 'bold'
              setFontWeight(value)
              activeObject.set('fontWeight', value)
              save()
            }}
          />
          <Button
            variant='outline'
            theme={fontStyle == 'italic' ? 'primary' : 'default'}
            icon={<div className='t-icon i-tabler:italic'></div>}
            onClick={() => {
              const value = fontStyle == 'italic' ? 'normal' : 'italic'
              setFontStyle(value)
              activeObject.set('fontStyle', value)
              save()
            }}
          />
          <Button
            variant='outline'
            theme={underline ? 'primary' : 'default'}
            icon={<div className='t-icon i-tabler:underline'></div>}
            onClick={() => {
              const value = underline ? false : true
              setUnderline(value)
              activeObject.set('underline', value)
              console.log(activeObject)
              save()
            }}
          />
        </div>
      </SiderItem>
    </>
  )
}

export const PosterText = {
  Btn: PosterBtn,
  Tools: PosterTools,
}
