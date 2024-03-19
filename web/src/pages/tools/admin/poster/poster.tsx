import React, {
  PropsWithChildren,
  ReactNode,
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
} from 'react'
import { Button, InputNumber, ColorPicker, Input, Tooltip } from 'tdesign-react/esm'
import { FabricJSCanvas, FabricJSEditor, useFabricJSEditor } from 'fabricjs-react'
import { fabric } from 'fabric'
import { useDrag, useDrop } from 'react-dnd'
import { useControllableValue } from 'ahooks'

import './style.scss'
import clsx from 'clsx'
import { UploadImage } from '@duxweb/dux-refine'
import { PosterText } from './poster/text'
import { PosterImage } from './poster/image'
import { PosteRectangle } from './poster/rectangle'

interface PosterContextProps {
  activeObject?: fabric.Object
  config: Record<string, any>
  setConfig: React.Dispatch<React.SetStateAction<Record<string, any>>>
  canvasObjects: fabric.Object[]
  setCanvasObjects: React.Dispatch<React.SetStateAction<fabric.Object[]>>
  setSelected: (index?: number) => void
  save: () => void
  editor?: FabricJSEditor
  selected?: number
}

export const PosterContext = createContext<PosterContextProps>({
  config: {},
  setConfig: () => {},
  canvasObjects: [],
  setCanvasObjects: () => {},
  setSelected: () => {},
  save: () => {},
})

interface PosterProps {
  value?: Record<string, any>
  defaultValue?: Record<string, any>
  onChange?: (value: Record<string, any>) => void
}

export interface PosterToolsProps {
  name: string
  Btn: () => ReactNode
  Tools?: () => ReactNode
}

export const Poster = ({ ...props }: PosterProps) => {
  const [tools, setTools] = useState<PosterToolsProps[]>([])
  const [value, setValue] = useControllableValue<Record<string, any>>(props)
  const { selectedObjects, editor, onReady } = useFabricJSEditor()
  const activeObject = selectedObjects?.[0]

  useEffect(() => {
    setTools([PosterText])
  }, [])

  const [config, setConfig] = useState<Record<string, any>>({
    width: 400,
    height: 600,
  })
  const [canvasObjects, setCanvasObjects] = useState<fabric.Object[]>([])
  const [selected, setSelected] = useState<number>()

  // useEffect(() => {
  //   // 防止库缓存，需要加载几次
  //   if (init > 2 || !value?.data) {
  //     return
  //   }
  //   setInit((v) => v + 1)
  //   setConfig(value?.config)
  //   editor?.canvas.loadFromJSON(value?.data, () => {
  //     editor.canvas.renderAll()
  //     updateCanvasObjects()
  //   })
  //   // eslint-disable-next-line react-hooks/exhaustive-deps
  // }, [editor, value])

  const save = useCallback(() => {
    editor?.canvas.renderAll()
    const json = editor?.canvas.toJSON(['name', 'label'])
    setValue({
      config: config,
      data: json,
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [editor])

  const updateCanvasObjects = useCallback(() => {
    setCanvasObjects(
      editor?.canvas.getObjects()?.map((item, index) => {
        if (!item.name) {
          item.name = '图层 ' + index
        }
        return item
      }) || [],
    )
  }, [editor])

  const handleSelection = (e: any) => {
    if (!e?.selected || e?.selected?.length > 1) {
      setSelected(undefined)
      return
    }
    const active = e?.selected?.[0]
    const index = editor?.canvas.getObjects().indexOf(active)
    setSelected(index)
  }

  useEffect(() => {
    if (!editor?.canvas) {
      return
    }

    editor.canvas.includeDefaultValues = false
    //editor.canvas.on('object:added', updateCanvasObjects)
    //editor.canvas.on('object:removed', updateCanvasObjects)
    editor.canvas.on('selection:created', handleSelection)
    editor.canvas.on('selection:updated', handleSelection)
    editor.canvas.on('selection:cleared', () => {
      setSelected(undefined)
    })
    editor.canvas.on('object:modified', function () {
      save()
    })

    return () => {
      //editor.canvas.off('object:added')
      //editor.canvas.off('object:removed')
      editor.canvas.off('selection:created')
      editor.canvas.off('selection:updated')
      editor.canvas.off('selection:modified')
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [editor?.canvas])

  const comp = useMemo(() => {
    const name = activeObject?.get('name')
    const info = tools.find((item) => {
      return item.name == name
    })
    return info
  }, [activeObject, tools])

  return (
    <PosterContext.Provider
      value={{
        config,
        setConfig,
        canvasObjects,
        selected,
        setSelected,
        setCanvasObjects,
        editor,
        activeObject,
        save,
      }}
    >
      <div className='w-full overflow-hidden border rounded border-component'>
        <div className='relative flex bg-component'>
          <div className='w-1 flex-1'>
            <div className='flex justify-between border-b p-2 shadow-sm bg-container border-component'>
              <div className='flex gap-2 gap-2 divide-x divide-gray-100'>
                <div className='flex'>
                  {tools.map((item, index) => {
                    return <item.Btn key={index} />
                  })}
                </div>
                <div className='flex px-4'>{comp?.Tools && <comp.Tools />}</div>
              </div>
              <div>
                <Tooltip content='上移一层'>
                  <Button
                    theme='default'
                    variant='text'
                    className='px-2'
                    icon={<div className='t-icon i-tabler:layers-subtract'></div>}
                  ></Button>
                </Tooltip>
                <Tooltip content='下移一层'>
                  <Button
                    theme='default'
                    variant='text'
                    className='px-2'
                    icon={<div className='t-icon i-tabler:layers-intersect'></div>}
                  ></Button>
                </Tooltip>
                <Tooltip content='删除图层'>
                  <Button
                    theme='default'
                    variant='text'
                    className='px-2'
                    icon={<div className='t-icon i-tabler:trash'></div>}
                  ></Button>
                </Tooltip>
              </div>
            </div>
            <div className='flex flex-1 justify-center overflow-auto p-10'>
              <div
                className='canvas-container'
                style={{
                  width: config.width,
                  height: config.height,
                }}
              >
                <FabricJSCanvas className='h-full w-full' onReady={onReady} />
              </div>
            </div>
          </div>
          <Sider />
        </div>
      </div>
    </PosterContext.Provider>
  )
}

const Sider = () => {
  const { editor, config, setConfig } = useContext(PosterContext)
  const activeObject = editor?.canvas?.getActiveObject()

  return (
    <div className='w-240px flex flex-none flex-col gap-2 border-l bg-container border-component'>
      <div className='h-50px flex items-center justify-center border-b px-4 border-component'>
        工具
      </div>
      <div className='flex flex-col divide-y divide-gray-3'>
        {!activeObject?.get('type') && (
          <>
            <SiderItem title='画布尺寸'>
              <div className='flex items-center gap-2'>
                <div className='w-1 flex-1'>
                  <InputNumber
                    theme='column'
                    value={config.width}
                    onChange={(v) =>
                      setConfig((old) => {
                        return { ...old, width: v }
                      })
                    }
                  />
                </div>
                <div className='flex-none'>x</div>
                <div className='w-1 flex-1'>
                  <InputNumber
                    theme='column'
                    value={config.height}
                    onChange={(v) =>
                      setConfig((old) => {
                        return { ...old, height: v }
                      })
                    }
                  />
                </div>
              </div>
            </SiderItem>
            <SiderItem title='背景图'>
              <UploadImage
                value={config?.image}
                onChange={(value) => {
                  fabric.Image.fromURL(value as string, (img) => {
                    if (!editor?.canvas) {
                      return
                    }
                    if (editor.canvas.width && img.width && editor.canvas.height && img.height) {
                      img.set({
                        scaleX: editor.canvas.width / img.width,
                        scaleY: editor.canvas?.height / img.height,
                      })
                    }
                    // 设置背景
                    editor?.canvas.setBackgroundImage(
                      img,
                      editor?.canvas.renderAll.bind(editor?.canvas),
                    )
                    editor?.canvas.renderAll()
                  })

                  setConfig((old) => {
                    return { ...old, image: value }
                  })
                }}
              />
            </SiderItem>
            <SiderItem title='背景色'>
              <ColorPicker
                format='HEX'
                value={config.color}
                onChange={(v) => {
                  editor?.canvas.setBackgroundColor(
                    v,
                    editor?.canvas.renderAll.bind(editor?.canvas),
                  )
                  setConfig((old) => {
                    return { ...old, color: v }
                  })
                }}
              />
            </SiderItem>
          </>
        )}

        <PosterImage.Tools />
        <PosteRectangle.Tools />
      </div>
    </div>
  )
}

export interface SiderItemProps extends PropsWithChildren {
  title: string
}
export const SiderItem = ({ title, children }: SiderItemProps) => {
  return (
    <div className='flex flex-col gap-2 p-4'>
      <div>{title}</div>
      <div>{children}</div>
    </div>
  )
}

interface LayerItemProps {
  name?: string
  desc?: string
  index?: number
  isSelected?: boolean
  moveLayer: (dragIndex: number, hoverIndex?: number) => void
  onRename?: (value: string) => void
  onClick?: () => void
  onRemove?: () => void
}
const LayerItem = ({
  name,
  index,
  isSelected,
  moveLayer,
  onRename,
  onClick,
  onRemove,
}: LayerItemProps) => {
  const layerType = 'LAYER'

  const [isDragging, setIsDragging] = useState(false)

  const ref = useDrag({
    type: layerType,
    item: () => {
      setIsDragging(true)
      return { index }
    },
    end: () => {
      setIsDragging(false)
    },
  })[1]

  const [, drop] = useDrop({
    accept: layerType,
    hover(item: Record<string, any>) {
      const dragIndex = item.index
      const hoverIndex = index
      if (dragIndex === hoverIndex) {
        return
      }
      moveLayer(dragIndex, hoverIndex)
      item.index = hoverIndex
    },
  })

  return (
    <div
      ref={(node) => drop(ref(node))}
      className={clsx([
        'flex items-center gap-2 border rounded p-2',
        isSelected ? 'border-brand bg-brand-1' : 'border-component',
      ])}
    >
      <div
        className='border rounded p-1 bg-container border-component'
        onClick={() => {
          if (isDragging) {
            return
          }
          onClick?.()
        }}
      >
        <div className='i-tabler:letter-t h-5 w-5'></div>
      </div>
      <div className='w-1 flex flex-1 flex-col gap-1'>
        <Input value={name} className='w-full' onChange={onRename} />
      </div>
      <div className='flex-none'>
        <Button
          className='px-2'
          variant='text'
          theme='danger'
          icon={<div className='i-tabler:x'></div>}
          onClick={onRemove}
        ></Button>
      </div>
    </div>
  )
}
