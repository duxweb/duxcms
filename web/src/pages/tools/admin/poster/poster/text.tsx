import { useCallback, useContext, useEffect, useMemo, useState } from 'react'
import { Button, ColorPicker, Popup, Tooltip, Dropdown, ColorPickerPanel } from 'tdesign-react/esm'
import { PosterContext, PosterToolsProps } from '../poster'
import { fabric } from 'fabric'

const PosterBtn = () => {
  const { canvas } = useContext(PosterContext)

  const onAddText = useCallback(() => {
    const text = new fabric.Textbox('请输入文本', {
      fontSize: 24,
      lockUniScaling: true,
    })
    text.set('name', 'text')

    canvas?.add(text)
  }, [canvas])

  return (
    <Tooltip content='文本'>
      <Button
        theme='default'
        variant='text'
        onClick={onAddText}
        className='px-2'
        icon={<div className='t-icon i-tabler:letter-t'></div>}
      ></Button>
    </Tooltip>
  )
}

const PosterTools = () => {
  const { canvas, activeObject } = useContext(PosterContext)
  const [fill, setFill] = useState(activeObject?.get('fill') || 'rgb(0,0,0)')

  return (
    <>
      <div>
        <Popup
          content={
            <ColorPickerPanel
              value={fill}
              format='RGB'
              onChange={(v) => {
                activeObject?.set('fill', v)
                canvas?.renderAll()
                setFill(v)
              }}
            />
          }
          trigger='hover'
        >
          <div>
            <Tooltip content='颜色'>
              <Button theme='default' variant='text' className='px-2'>
                <div
                  className='t-icon'
                  style={{
                    backgroundColor: activeObject?.get('fill'),
                  }}
                ></div>
              </Button>
            </Tooltip>
          </div>
        </Popup>
      </div>

      <div>
        <Dropdown
          options={[
            {
              content: '左对齐',
              value: 'left',
              prefixIcon: <div className='t-icon i-tabler:align-left' />,
            },
            {
              content: '居中',
              value: 'center',
              prefixIcon: <div className='t-icon i-tabler:align-center' />,
            },
            {
              content: '右对齐',
              value: 'right',
              prefixIcon: <div className='t-icon i-tabler:align-right' />,
            },
          ]}
          onClick={(data) => {
            activeObject?.set('textAlign', data.value)
            canvas?.renderAll()
          }}
        >
          <div>
            <Tooltip content='对齐'>
              <Button
                theme='default'
                variant='text'
                className='px-2'
                icon={<div className='t-icon i-tabler:align-justified'></div>}
              ></Button>
            </Tooltip>
          </div>
        </Dropdown>
      </div>

      <div>
        <Dropdown
          options={[
            {
              content: '加粗',
              value: 'bold',
              prefixIcon: <div className='t-icon i-tabler:bold' />,
            },
            {
              content: '斜体',
              value: 'italic',
              prefixIcon: <div className='t-icon i-tabler:italic' />,
            },
            {
              content: '下划线',
              value: 'underline',
              prefixIcon: <div className='t-icon i-tabler:underline' />,
            },
          ]}
          onClick={(data) => {
            switch (data.value) {
              case 'bold':
                activeObject?.set(
                  'fontWeight',
                  activeObject.fontWeight == 'bold' ? 'normal' : 'bold',
                )
                break
              case 'italic':
                activeObject?.set(
                  'fontStyle',
                  activeObject.fontStyle == 'italic' ? 'normal' : 'italic',
                )
                break
              case 'underline':
                activeObject?.set('underline', !activeObject?.underline)
                break
            }
            canvas?.renderAll()
          }}
        >
          <div>
            <Tooltip content='样式'>
              <Button
                theme='default'
                variant='text'
                className='px-2'
                icon={<div className='t-icon i-tabler:typography'></div>}
              ></Button>
            </Tooltip>
          </div>
        </Dropdown>
      </div>
    </>
  )
}

export const PosterText: PosterToolsProps = {
  name: 'text',
  Btn: PosterBtn,
  Tools: PosterTools,
}
