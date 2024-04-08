import { useClient, appHook } from '@duxweb/dux-refine'
import { Button, Input, MessagePlugin, FormInstanceFunctions } from 'tdesign-react/esm'
import { useTranslate } from '@refinedev/core'
import tinymce from 'tinymce/tinymce'
import { useCallback, useState } from 'react'

interface SeleFormProps {
  form?: FormInstanceFunctions
}
const SaleForm = ({ form }: SeleFormProps) => {
  const [extractUrl, setExtractUrl] = useState<string>('')
  const translate = useTranslate()
  const { request } = useClient()

  const extract = useCallback(() => {
    request('contentExtend/spider/content', 'post', {
      data: {
        url: extractUrl,
      },
    }).then((e) => {
      if (e.code !== 200) {
        MessagePlugin.error(e?.message)
        return
      }
      form?.setFieldsValue({
        title: e?.data?.title,
        content: e?.data?.content_html,
      })
      tinymce?.activeEditor?.fire('pasteHtml', { content: e?.data?.content_html || '' })
    })
  }, [extractUrl, form, request])

  return (
    <div className='rounded bg-gray-2 p-4 border-component dark:bg-gray-11'>
      <div className='mb-2'>{translate('contentExtend.spider.name')}</div>
      <div className='mb-2 flex gap-2'>
        <div className='w-1 flex-1'>
          <Input
            placeholder={translate('contentExtend.spider.validates.uri')}
            value={extractUrl}
            onChange={(v) => setExtractUrl(v)}
          />
        </div>
        <div className='flex-none'>
          <Button onClick={extract}>{translate('contentExtend.spider.fields.extract')}</Button>
        </div>
      </div>
      <div className='text-placeholder'>{translate('contentExtend.spider.helps.extract')}</div>
    </div>
  )
}

appHook.add('admin.content.article.form.helper', <SaleForm />)
