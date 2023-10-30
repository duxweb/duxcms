import React, { useCallback, useState } from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Link, Popconfirm, Tag, Tooltip, Loading, Dialog } from 'tdesign-react/esm'
import { PageTable, MediaText, useClient } from '@duxweb/dux-refine'
import dayjs from 'dayjs'

const List = () => {
  const translate = useTranslate()
  const { mutate } = useDelete()
  const [loading, setLoading] = useState(false)
  const [open, setOpen] = useState(false)
  const [log, setLog] = useState('')
  const client = useClient()

  const update = useCallback((name: string) => {
    client
      .request('cloud/apps', 'put', {
        data: {
          name: name,
        },
      })
      .then((res) => {
        setLog(res?.data?.content)
        setOpen(true)
      })
      .finally(() => {
        setLoading(false)
      })
  }, [])

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'name',
        title: translate('cloud.apps.fields.name'),
        minWidth: 300,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              <MediaText.Image src={row.icon}></MediaText.Image>
              <MediaText.Title>{row.title}</MediaText.Title>
              <MediaText.Desc>{row.desc}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'time',
        title: translate('cloud.apps.fields.time'),
        cell: ({ row }) => {
          return <>{dayjs(row.time * 1000).format('YYYY-MM-DD HH:mm')}</>
        },
      },
      {
        colKey: 'update',
        title: translate('cloud.apps.fields.update'),
        cell: ({ row }) => {
          return (
            <>
              {!row?.update ? (
                <Tag variant='outline'>暂无更新</Tag>
              ) : (
                <Popconfirm
                  content={translate('buttons.confirm')}
                  destroyOnClose
                  placement='top'
                  showArrow
                  theme='default'
                  onConfirm={() => {
                    setLoading(true)
                    update(row.name)
                  }}
                >
                  <span>
                    <Tooltip content={dayjs(row.time * 1000).format('YYYY-MM-DD HH:mm')}>
                      <Tag theme='warning' variant='outline' className='cursor-pointer'>
                        有更新
                      </Tag>
                    </Tooltip>
                  </span>
                </Popconfirm>
              )}
            </>
          )
        },
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 160,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Link theme='primary' href={`https://www.dux.plus/apps/` + row.id} target='_black'>
                {translate('buttons.show')}
              </Link>
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'cloud.apps',
                    id: row.id,
                  })
                }}
              >
                <Link theme='danger'>{translate('buttons.delete')}</Link>
              </Popconfirm>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  return (
    <>
      <PageTable
        columns={columns}
        table={{
          rowKey: 'id',
          pagination: {
            disabled: true,
          },
        }}
        title={translate('cloud.apps.name')}
      />
      <Dialog
        className='app-modal'
        header='日志'
        footer={false}
        visible={open}
        onClose={() => {
          setOpen(false)
          window.location.reload()
        }}
      >
        <div className='p-4'>
          <pre className='overflow-auto rounded-lg p-4 bg-component'>{log}</pre>
        </div>
      </Dialog>
      <Loading
        loading={loading}
        fullscreen
        preventScrollThrough={true}
        text='更新中，请稍等'
      ></Loading>
    </>
  )
}

export default List
