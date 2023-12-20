import { useTranslate } from '@refinedev/core'
import { FormModal, useSelect } from '@duxweb/dux-refine'
import { Button, Form, Input, Select, Textarea } from 'tdesign-react/esm'
import { MinusCircleIcon } from 'tdesign-icons-react'
import { useCallback } from 'react'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  const { options, onSearch, queryResult } = useSelect({
    resource: 'sms.tpl.method',
    optionLabel: 'name',
    optionValue: 'value',
  })

  const getType = useCallback(
    (method?: unknown) => {
      if (!method) {
        return undefined
      }
      const info = (queryResult?.data?.data as Record<string, any>[])?.find?.(
        (item: Record<string, any>) => item.value == method,
      )
      console.log('info', info)
      return info?.type
    },
    [queryResult?.data],
  )

  const [form] = Form.useForm()

  return (
    <FormModal id={props?.id} form={form}>
      <Form.FormItem label={translate('sms.tpl.fields.name')} name='name'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('sms.tpl.fields.method')} name='method'>
        <Select filterable onSearch={onSearch} options={options} />
      </Form.FormItem>
      {getType(form?.getFieldValue?.('method')) ? (
        <>
          <Form.FormItem
            name='tpl'
            label={translate('sms.tpl.fields.tpl')}
            help={'参数值可使用 {变量名} 来代替系统变量，如：验证码 {code}'}
          >
            <Input />
          </Form.FormItem>

          <Form.FormList name='params'>
            {(fields, { add, remove }) => (
              <>
                {fields.map(({ key, name, ...restField }) => (
                  <div className='flex gap-4' key={key}>
                    <Form.FormItem
                      {...restField}
                      name={[name, 'name']}
                      label={translate('sms.tpl.fields.paramsName')}
                      rules={[{ required: true, type: 'error' }]}
                      className='flex-grow'
                    >
                      <Input />
                    </Form.FormItem>

                    <Form.FormItem
                      {...restField}
                      name={[name, 'value']}
                      label={translate('sms.tpl.fields.paramsValue')}
                      rules={[{ required: true, type: 'error' }]}
                      className='flex-grow'
                    >
                      <Input />
                    </Form.FormItem>

                    <div className='mt-9 flex-none'>
                      <MinusCircleIcon
                        size='20px'
                        style={{ cursor: 'pointer' }}
                        onClick={() => remove(name)}
                      />
                    </div>
                  </div>
                ))}
                <Form.FormItem>
                  <Button theme='default' variant='dashed' onClick={() => add({})}>
                    增加参数
                  </Button>
                </Form.FormItem>
              </>
            )}
          </Form.FormList>
        </>
      ) : (
        <Form.FormItem
          name='content'
          label={translate('sms.tpl.fields.content')}
          help={'模板内容使用 {变量名} 来代替系统变量，如：验证码 {code}'}
        >
          <Textarea />
        </Form.FormItem>
      )}
    </FormModal>
  )
}

export default Page
