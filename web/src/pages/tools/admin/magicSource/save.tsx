import { useTranslate } from '@refinedev/core'
import { CodeEditor, FormModal, Listform, SelectAsync } from '@duxweb/dux-refine'
import { Form, Input, Radio } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id}>
      <Form.FormItem label={translate('tools.magicSource.fields.name')} name='name'>
        <Input />
      </Form.FormItem>
      <Form.FormItem
        label={translate('tools.magicSource.fields.type')}
        name='type'
        initialData={'data'}
      >
        <Radio.Group>
          <Radio value={'data'}>{translate('tools.magicSource.fields.data')}</Radio>
          <Radio value={'remote'}>{translate('tools.magicSource.fields.remote')}</Radio>
          <Radio value={'source'}>{translate('tools.magicSource.fields.source')}</Radio>
        </Radio.Group>
      </Form.FormItem>
      <Form.FormItem shouldUpdate>
        {({ getFieldValue }) => {
          if (getFieldValue('type') === 'data') {
            return (
              <Form.FormItem
                label={translate('tools.magicSource.fields.data')}
                name='data'
                key='data'
              >
                <CodeEditor type='json' />
              </Form.FormItem>
            )
          }
          if (getFieldValue('type') === 'remote') {
            return (
              <>
                <Form.FormItem
                  label={translate('tools.magicSource.fields.url')}
                  name={['data', 'url']}
                  key={'data.url'}
                >
                  <Input />
                </Form.FormItem>

                <Form.FormItem
                  label={translate('tools.magicSource.fields.cache')}
                  name={['data', 'cache']}
                  key={'data.cache'}
                >
                  <Input />
                </Form.FormItem>

                <Form.FormItem
                  label={translate('tools.magicSource.fields.header')}
                  name={['data', 'headers']}
                  key={'data.headers'}
                >
                  <Listform
                    options={[
                      {
                        title: translate('tools.magicSource.fields.params_name'),
                        component: <Input />,
                        field: 'name',
                      },
                      {
                        title: translate('tools.magicSource.fields.params_value'),
                        component: <Input />,
                        field: 'value',
                      },
                    ]}
                  />
                </Form.FormItem>
                <Form.FormItem
                  label={translate('tools.magicSource.fields.method')}
                  name={['data', 'method']}
                  key={'data.method'}
                  initialData={'get'}
                >
                  <Radio.Group>
                    <Radio value={'get'}>GET</Radio>
                    <Radio value={'post'}>POST</Radio>
                  </Radio.Group>
                </Form.FormItem>

                {getFieldValue(['data', 'method']) == 'post' && (
                  <>
                    <Form.FormItem
                      label={translate('tools.magicSource.fields.req_type')}
                      name={['data', 'method_type']}
                      key={'data.method_type'}
                      initialData={'form'}
                    >
                      <Radio.Group>
                        <Radio value={'form'}>Form</Radio>
                        <Radio value={'post'}>Json</Radio>
                      </Radio.Group>
                    </Form.FormItem>

                    <Form.FormItem
                      label={translate('tools.magicSource.fields.req_data')}
                      name={['data', 'data']}
                      key={'data.data'}
                    >
                      <Listform
                        options={[
                          {
                            title: translate('tools.magicSource.fields.params_name'),
                            component: <Input />,
                            field: 'name',
                          },
                          {
                            title: translate('tools.magicSource.fields.params_value'),
                            component: <Input />,
                            field: 'value',
                          },
                        ]}
                      />
                    </Form.FormItem>
                  </>
                )}
                <Form.FormItem
                  label={translate('tools.magicSource.fields.req_filed')}
                  name={['data', 'fields']}
                  key={'data.fields'}
                >
                  <Input />
                </Form.FormItem>
              </>
            )
          }
          if (getFieldValue('type') === 'source') {
            return (
              <>
                <Form.FormItem
                  label={translate('tools.magicSource.fields.name')}
                  name={['data', 'name']}
                  key={'data.name'}
                >
                  <SelectAsync
                    url='tools/magicSource/system'
                    optionLabel='label'
                    optionValue='value'
                  />
                </Form.FormItem>
              </>
            )
          }
          return <></>
        }}
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
