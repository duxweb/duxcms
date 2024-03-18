import { FormModal, useClient } from '@duxweb/dux-refine'
import { Button, Form, Input, NamePath, Tabs, Textarea } from 'tdesign-react/esm'
import { useEffect, useState } from 'react'
import { Icon } from 'tdesign-icons-react'
import { useTranslate } from '@refinedev/core'

const Page = (props: Record<string, any>) => {
  const [result, setResult] = useState<Record<string, any> | undefined>({})

  const { request } = useClient()

  useEffect(() => {
    request(`cms/theme/${props?.id}/config`).then((res) => {
      setResult(res?.data)
    })
  }, [])

  return (
    <FormModal id={props?.id} padding={false}>
      <Tabs placement={'top'} size={'medium'} defaultValue={0}>
        {Object.entries<Record<string, any>>(result || {})
          .filter(([key]) => {
            if (key == 'theme') {
              return false
            }
            return true
          })
          .map(([key, value], index) => {
            const fileds = Object.entries<Record<string, any>>(value?.fields || {})
            return (
              <Tabs.TabPanel value={index} label={value?.label} key={index} destroyOnHide={false}>
                <div className='p-5'>
                  {fileds.map(([name, field], fieldIndex) => {
                    if (field?.type == 'textarea') {
                      return (
                        <Form.FormItem key={fieldIndex} name={[key, name]} label={field?.label}>
                          <Textarea />
                        </Form.FormItem>
                      )
                    }
                    if (field?.type == 'text') {
                      return (
                        <Form.FormItem key={fieldIndex} name={[key, name]} label={field?.label}>
                          <Input />
                        </Form.FormItem>
                      )
                    }
                    if (field?.type == 'list') {
                      return <ItemList key={fieldIndex} name={[key, name]} items={field?.fields} />
                    }
                    return
                  })}
                </div>
              </Tabs.TabPanel>
            )
          })}
      </Tabs>
    </FormModal>
  )
}

interface ItemListProps {
  name?: NamePath
  items?: Record<string, any>
}
const ItemList = ({ name, items }: ItemListProps) => {
  const translate = useTranslate()

  return (
    <Form.FormList name={name}>
      {(fields, { add, remove }) => (
        <>
          {fields.map(({ key, name, ...restField }) => (
            <div key={key} className='flex gap-4'>
              {Object.entries<string>(items || {}).map(([itemName, itemLabel], itemIndex) => (
                <Form.FormItem
                  {...restField}
                  key={itemIndex}
                  name={[name, itemName]}
                  label={itemLabel}
                  className='w-0 flex-1'
                >
                  <Input className='w-full' />
                </Form.FormItem>
              ))}
              <Form.FormItem label=' ' className='flex-none'>
                <Icon
                  size='20px'
                  name='delete'
                  style={{ cursor: 'pointer' }}
                  onClick={() => remove(name)}
                />
              </Form.FormItem>
            </div>
          ))}
          <Form.FormItem>
            <Button theme='default' variant='dashed' onClick={() => add({})}>
              {translate('cms.theme.addField')}
            </Button>
          </Form.FormItem>
        </>
      )}
    </Form.FormList>
  )
}

export default Page
