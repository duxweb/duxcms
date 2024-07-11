import { useTranslate } from '@refinedev/core'
import { FormPage, FormPageItem, SelectAsync } from '@duxweb/dux-refine'
import { Tabs } from 'tdesign-react/esm'

const Index = () => {
  const translate = useTranslate()

  return (
    <FormPage
      rest
      className='app-card-tabs'
      action='edit'
      id={0}
      useFormProps={{
        meta: {
          mode: 'page',
        },
      }}
    >
      <Tabs defaultValue={0}>
        <Tabs.TabPanel value={0} label={'基本设置'} destroyOnHide={false}>
          <FormPageItem label={'文章海报'} name='article_poster' help={'文章分享推广海报'}>
            <SelectAsync url='poster/design' optionLabel='title' optionValue='id' />
          </FormPageItem>
        </Tabs.TabPanel>
      </Tabs>
    </FormPage>
  )
}

export default Index
