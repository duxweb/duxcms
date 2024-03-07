import { useCallback, useContext, useEffect, useState } from 'react'
import { Button, Dialog, Slider } from 'tdesign-react/esm'
import { PosterContext, SiderItem } from '../poster'
import { FileManage } from '@duxweb/dux-refine'
import { fabric } from 'fabric'

const PosterBtn = () => {
  const { editor } = useContext(PosterContext)
  const [visible, setVisible] = useState(false)

  const onAddImage = useCallback(
    (url: string) => {
      fabric.Image.fromURL(url, (img) => {
        img.scale(1).set({
          left: 10,
          top: 10,
        })

        editor?.canvas.add(img)
      })
    },
    [editor],
  )

  return (
    <>
      <Button
        theme='default'
        variant='text'
        onClick={() => setVisible(true)}
        icon={<div className='t-icon i-tabler:photo'></div>}
      >
        图片
      </Button>

      <Dialog
        className='app-modal'
        width={'800px'}
        header={'选择图片'}
        visible={visible}
        destroyOnClose
        footer={false}
        onClose={() => setVisible(false)}
      >
        <FileManage
          accept={'image/*'}
          mode={'single'}
          onClose={() => setVisible(false)}
          onChange={(data: Record<string, any>[]) => {
            onAddImage(data[0].url as string)
          }}
        />
      </Dialog>
    </>
  )
}

const PosterTools = () => {
  const { editor, save } = useContext(PosterContext)
  const activeObject = editor?.canvas?.getActiveObject() as Record<string, any>
  const [opacity, setOpacity] = useState<number>(1)

  useEffect(() => {
    setOpacity(activeObject?.opacity || 1)
  }, [activeObject])

  if (activeObject?.get('type') !== 'image') {
    return <></>
  }

  return (
    <>
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
    </>
  )
}

export const PosterImage = {
  Btn: PosterBtn,
  Tools: PosterTools,
}
